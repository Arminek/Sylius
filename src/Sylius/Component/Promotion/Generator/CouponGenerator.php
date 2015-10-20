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
    protected $couponRepository;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var array
     */
    protected $generatedCoupons;

    protected $instructionValidator;

    /**
     * @param RepositoryInterface    $couponRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(RepositoryInterface $couponRepository, EntityManagerInterface $entityManager)
    {
        $this->couponRepository = $couponRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(PromotionInterface $promotion, InstructionInterface $instruction)
    {
        $usageLimit = $instruction->getUsageLimit();
        $codeLength = $instruction->getCodeLength();
        $expiresAt = $instruction->getExpiresAt();
        $amount = $instruction->getAmount();
        $this->generatedCoupons = $this->getAlreadyGeneratedCoupons();

        $this->isGenerationPossible($codeLength, $amount);
        
        for ($i = 0; $i < $amount; $i++) {
            $coupon = $this->couponRepository->createNew();
            $coupon->setPromotion($promotion);
            $coupon->setCode($this->generateUniqueCode($codeLength));
            $coupon->setUsageLimit($usageLimit);
            $coupon->setExpiresAt($expiresAt);
            
            $this->generatedCoupons[] = $coupon;

            $this->entityManager->persist($coupon);
        }

        $this->entityManager->flush();

        return $this->generatedCoupons;
    }

    /**
     * {@inheritdoc}
     */
    public function generateUniqueCode($codeLength)
    {
        $code = null;

        if (1 > $codeLength || 40 < $codeLength) {
            throw new \InvalidArgumentException(
                sprintf('Invalid code length should be between 1 and 40. "%d" given', $codeLength)
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
        foreach($this->generatedCoupons as $coupon) {
            if ($coupon->getCode() === $code) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param int $expectedCodeLength
     * @param int $expectedAmount
     *
     * @throws \InvalidArgumentException
     */
    protected function isGenerationPossible($expectedCodeLength, $expectedAmount)
    {
        $generatedAmount = $this->countCouponsByCodeLength($expectedCodeLength);
        $possibleAmount = pow(16, $expectedCodeLength) - $generatedAmount;

        if ($possibleAmount < $expectedAmount) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid coupon code length or coupons amount. Already generated coupons "%d", '.
                    'possible coupons amount "%d", expected coupons amount "%d"',
                    $generatedAmount,
                    $possibleAmount,
                    $expectedAmount
                )
            );
        }
    }

    /**
     * @return array
     */
    protected function getAlreadyGeneratedCoupons()
    {
        $this->entityManager->getFilters()->disable('softdeleteable');
        $coupons = $this->couponRepository->findAll();
        $this->entityManager->getFilters()->enable('softdeleteable');

        return $coupons;
    }

    /**
     * @param $codeLength
     *
     * @return int
     */
    protected function countCouponsByCodeLength($codeLength)
    {
        $couponsAmount = 0;
        foreach ($this->generatedCoupons as $coupon)
        {
            if (strlen($coupon->getCode()) === $codeLength)
            {
                $couponsAmount++;
            }
        }

        return $couponsAmount;
    }
}
