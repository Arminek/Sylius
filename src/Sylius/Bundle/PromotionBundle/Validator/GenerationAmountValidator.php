<?php

/*
 * This file is part of the Lakion package.
 *
 * (c) Lakion
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionBundle\Validator;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Promotion\Generator\InstructionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class GenerationAmountValidator extends ConstraintValidator
{
    private $couponRepository;

    public function __construct(EntityRepository $couponRepository)
    {
        $this->couponRepository = $couponRepository;
    }

    /**
     * @param InstructionInterface $protocol
     * @param Constraint $constraint
     */
    public function validate($protocol, Constraint $constraint)
    {
        if ($this->isGenerationPossible($protocol)) {
            $this->context->addViolation(
                $constraint->message,
                array(
                    '%expectedAmount%' => $protocol->getAmount()
                )
            );
        }
    }

    private function isGenerationPossible(InstructionInterface $instruction)
    {
        $expectedAmount = $instruction->getAmount();
        $expectedCodeLength = $instruction->getCodeLength();

        $generatedAmount = $this->countCouponsByCodeLength($expectedCodeLength);
        $possibleAmount = pow(16, $expectedCodeLength) - $generatedAmount;
        $type = gettype($possibleAmount);
        return $possibleAmount < $expectedAmount;
    }

    private function countCouponsByCodeLength($codeLength)
    {
        $coupons = $this->couponRepository->findAll();
        $couponsAmount = 0;
        foreach ($coupons as $coupon)
        {
            if (strlen($coupon->getCode()) === $codeLength)
            {
                $couponsAmount++;
            }
        }

        return $couponsAmount;
    }
}
