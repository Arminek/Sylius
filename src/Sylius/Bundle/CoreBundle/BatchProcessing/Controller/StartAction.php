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

namespace Sylius\Bundle\CoreBundle\BatchProcessing\Controller;

use Sylius\Bundle\CoreBundle\DependencyInjection\ProcessManagerRegistry;
use Sylius\Bundle\CoreBundle\Repository\ProcessRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

final class StartAction
{
    private ProcessRepository $repository;
    private ProcessManagerRegistry $registry;
    private RouterInterface $router;

    public function __construct(ProcessRepository $repository, ProcessManagerRegistry $registry, RouterInterface $router)
    {
        $this->repository = $repository;
        $this->registry = $registry;
        $this->router = $router;
    }

    public function __invoke(int $id): Response
    {
        $process = $this->repository->find($id);

        if (null !== $process) {
            $this->registry->get($process->getProcessManagerId())->start($process);
        }

        return new RedirectResponse($this->router->generate('sylius_admin_batch_processing_index'));
    }
}
