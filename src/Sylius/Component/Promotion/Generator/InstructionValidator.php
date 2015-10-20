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

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class InstructionValidator
{
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