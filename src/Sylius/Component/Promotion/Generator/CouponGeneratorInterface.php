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

use Sylius\Component\Promotion\Model\CouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;

/**
 * Coupon generator interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface CouponGeneratorInterface
{
    /**
     * Generate coupons for the promotion based on the instruction.
     *
     * @param PromotionInterface   $promotion
     * @param InstructionInterface $instruction
     *
     * @return CouponInterface[]   $coupons
     */
    public function generate(PromotionInterface $promotion, InstructionInterface $instruction);

    /**
     * @param int $codeLength
     *
     * @return string
     */
    public function generateUniqueCode($codeLength);
}
