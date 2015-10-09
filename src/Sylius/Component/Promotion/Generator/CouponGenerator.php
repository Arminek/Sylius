<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Generator;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Default coupon generator.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CouponGenerator implements CouponGeneratorInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var EntityManagerInterface
     */
    protected $manager;

    /**
     * @param RepositoryInterface    $repository
     * @param EntityManagerInterface $manager
     */
    public function __construct(RepositoryInterface $repository, EntityManagerInterface $manager)
    {
        $this->repository = $repository;
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(PromotionInterface $promotion, InstructionInterface $instruction)
    {
        $generatedCoupons = array();
        $usageLimit = $instruction->getUsageLimit();
        $codeLength = $instruction->getCodeLength();
        $expiresAt = $instruction->getExpiresAt();
        
        for ($i = 0, $amount = $instruction->getAmount(); $i < $amount; $i++) {
            $coupon = $this->repository->createNew();
            $coupon->setPromotion($promotion);
            $coupon->setCode($this->generateUniqueCode($codeLength));
            $coupon->setUsageLimit($usageLimit);
            $coupon->setExpiresAt($expiresAt);
            
            $generatedCoupons[] = $coupon;

            $this->manager->persist($coupon);
        }

        $this->manager->flush();

        return $generatedCoupons;
    }

    /**
     * {@inheritdoc}
     */
    public function generateUniqueCode($codeLength)
    {
        $code = null;

        if (1 > $codeLength || 40 < $codeLength) {
            throw new \InvalidArgumentException(
                'Invalid code length should be between 1 and 40'
            );
        }

        do {
            $hash = sha1(microtime(true));
            $code = strtoupper(substr($hash, 0, $codeLength));
        } while ($this->isUsedCode($code));

        return $code;
    }

    /**
     * @param string $code
     *
     * @return Boolean
     */
    protected function isUsedCode($code)
    {
        $this->manager->getFilters()->disable('softdeleteable');

        $isUsed = null !== $this->repository->findOneBy(array('code' => $code));

        $this->manager->getFilters()->enable('softdeleteable');

        return $isUsed;
    }
}
