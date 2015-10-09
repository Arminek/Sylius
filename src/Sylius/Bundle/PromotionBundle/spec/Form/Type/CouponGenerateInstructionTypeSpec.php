<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PromotionBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class CouponGenerateInstructionTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Instruction', array('sylius'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionBundle\Form\Type\CouponGenerateInstructionType');
    }

    function it_should_be_a_form_type()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    function it_should_build_form_with_proper_fields(FormBuilder $builder)
    {
        $builder
            ->add('amount', 'integer', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('usageLimit', 'integer', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('expiresAt', 'date', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('codeLength', 'integer', Argument::any())
            ->willReturn($builder)
        ;

        $this->buildForm($builder, array());
    }

    function it_should_define_assigned_data_class(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class'        => 'Instruction',
                'validation_groups' => array('sylius'),
            ))
            ->shouldBeCalled()
        ;

        $this->setDefaultOptions($resolver);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_promotion_coupon_generate_instruction');
    }
}
