<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\PromotionBundle\BatchProcessing;

use Sylius\Bundle\CoreBundle\BatchProcessing\Messages\JobFinished;
use Sylius\Bundle\CoreBundle\BatchProcessing\Messages\JobStarted;
use Sylius\Bundle\CoreBundle\BatchProcessing\Messages\ProcessFinished;
use Sylius\Bundle\CoreBundle\BatchProcessing\Messages\ProcessStarted;
use Sylius\Bundle\CoreBundle\BatchProcessing\ProcessManager;
use Sylius\Bundle\CoreBundle\Entity\Job;
use Sylius\Bundle\CoreBundle\Entity\Process;
use Sylius\Bundle\CoreBundle\Entity\Status;
use Sylius\Bundle\CoreBundle\Repository\JobRepository;
use Sylius\Bundle\CoreBundle\Repository\ProcessRepository;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Repository\PromotionRepositoryInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

final class CatalogPromotionManager implements ProcessManager
{
    private const LIMIT = 100;

    private ProcessRepository $repository;
    private MessageBusInterface $bus;
    private PromotionRepositoryInterface $promotionRepository;
    private ProductRepositoryInterface $productRepository;
    private TaxonRepositoryInterface $taxonRepository;
    private JobRepository $jobRepository;

    public function __construct(
        ProcessRepository $repository,
        MessageBusInterface $bus,
        PromotionRepositoryInterface $promotionRepository,
        ProductRepositoryInterface $productRepository,
        TaxonRepositoryInterface $taxonRepository,
        JobRepository $jobRepository
    ) {
        $this->repository = $repository;
        $this->bus = $bus;
        $this->promotionRepository = $promotionRepository;
        $this->productRepository = $productRepository;
        $this->taxonRepository = $taxonRepository;
        $this->jobRepository = $jobRepository;
    }

    public function start(Process $process): void
    {
        $this->bus->dispatch(new ProcessStarted($process->getId()));
    }

    public function handleProcessStarted(ProcessStarted $event): void
    {
        dump($event);
        $process = $this->repository->find($event->processId);
        foreach ($process->getJobs() as $job) {
            $process->removeJob($job);
        }

        if (null === $process) {
            return;
        }
        if ($process->getStatus() === Status::NOT_ACTIVE) {
            $process->setStatus(Status::IN_PROGRESS);
        }

        /** @var PromotionInterface $promotion */
        $promotion = $this->promotionRepository->findOneBy(['code' => 'catalog']);
        if(!$promotion->isActive() || ($process->getData()['alreadyApplied'] ?? false)) {
            $this->bus->dispatch(new ProcessFinished($process->getId()));
            return;
        }

        $taxons = [];
        $amount = 0;
        foreach ($promotion->getActions() as $action) {
            $amount = $action->getConfiguration()['percentage'] ?? 0;
        }
        foreach ($promotion->getRules() as $rule) {
            $taxons = array_merge($taxons, $rule->getConfiguration()['taxons'] ?? []);
        }
        $process->setData(['amount' => $amount, 'taxons' => $taxons]);

        $taxons = array_map(
            function (string $code): ?TaxonInterface {
                return $this->taxonRepository->findOneBy(['code' => $code]);
            },
            $taxons
        );
        $taxons = array_filter($taxons);

        /** @var TaxonInterface $taxon */
        foreach ($taxons as $taxon) {
            $productAmount = (int) $this->productRepository
                ->createQueryBuilder('product')
                ->select('COUNT(product)')
                ->innerJoin('product.productTaxons', 'productTaxon')
                ->andWhere('productTaxon.taxon = :taxonId')
                ->setParameter('taxonId', $taxon->getId())
                ->getQuery()
                ->getSingleScalarResult();
//                ->innerJoin('o.productTaxons', 'productTaxon')
//                ->andWhere('productTaxon.taxon = :taxonId')
//                ->setParameter('taxonId', $taxon->getId());
            dump($productAmount);

            $jobAmount = (int)ceil($productAmount / self::LIMIT );
            for ($i = 0; $i < $jobAmount; $i++) {
                $currentPage = $i+1;
                $offset = $currentPage * self::LIMIT - self::LIMIT;
                $job = (new Job())->setData([
                    'offset' => $offset,
                    'currentPage' => $currentPage,
                    'limit' => self::LIMIT,
                    'taxonId' => $taxon->getId(),
                ])->setName(
                    sprintf(
                        'Discount products: %s taxon, %s page, %s offset',
                        $taxon->getName(),
                        $currentPage,
                        $offset
                    )
                );
                $process->addJob($job);
            }
        }
        $this->repository->add($process);

        foreach ($process->getJobs() as $job) {
            $this->bus->dispatch(
                new Envelope(new JobStarted($process->getId(), $job->getId()))
            );
        }
    }

    public function handleJobStarted(JobStarted $event): void
    {
        dump($event);
        $job = $this->jobRepository->find($event->jobId);
        $process = $job->getProcess();
        if (!$process->isFinished()) {
            $data = $job->getData();
            $processData = $process->getData();
            $productsFromTaxon = $this->productRepository
                ->createListQueryBuilder('en_US', $data['taxonId'])
                ->setMaxResults($data['limit'])
                ->addOrderBy('o.id', 'DESC')
                ->setFirstResult($data['offset'])
                ->getQuery()
                ->getResult();
            /** @var ProductInterface $product */
            foreach ($productsFromTaxon as $product) {
                /** @var ProductVariantInterface $variant */
                foreach ($product->getVariants() as $variant) {
                    foreach ($variant->getChannelPricings() as $channelPricing) {
                        $currentPrice = $channelPricing->getPrice();
                        $discounted = $currentPrice - (int)($currentPrice * $processData['amount']);
                        $channelPricing->setOriginalPrice($currentPrice);
                        $channelPricing->setPrice($discounted);
                    }
                }
                $this->productRepository->add($product);
            }
            $job->setStatus(Status::FINISHED);
            $this->repository->add($process);
            $this->bus->dispatch(new JobFinished($process->getId(), $job->getId()));
        }
    }

    public function handleJobFinished(JobFinished $event): void
    {
        dump($event);
        $job = $this->jobRepository->find($event->jobId);
        $process = $job->getProcess();
        if (($process->getData()['finished'] ?? false)) {
            return;
        }
        $process->recalculateProgress();
        if ($process->isFinished()) {
            $process->setData(array_merge($process->getData(), ['finished' => true]));
            $this->bus->dispatch(new ProcessFinished($process->getId()));
        }
        $this->repository->add($process);
    }

    public function handleProcessFinished(ProcessFinished $event): void
    {
        dump($event);
        $process = $this->repository->find($event->processId);
        $process->setStatus(Status::FINISHED);
        $process->setData(array_merge($process->getData(), ['alreadyApplied' => true]));

        $this->repository->add($process);
        $this->bus->dispatch(new Envelope(new ProcessStarted($event->processId), [new DelayStamp(60000)]));
    }

    public static function getHandledMessages(): iterable
    {
        yield ProcessStarted::class => ['method' => 'handleProcessStarted'];
        yield ProcessFinished::class => ['method' => 'handleProcessFinished'];
        yield JobStarted::class => ['method' => 'handleJobStarted'];
        yield JobFinished::class => ['method' => 'handleJobFinished'];
    }
}
