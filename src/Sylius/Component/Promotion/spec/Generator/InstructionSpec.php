<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Promotion\Generator;

use PhpSpec\ObjectBehavior;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class InstructionSpec extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Promotion\Generator\Instruction');
    }

    function it_should_implement_instruction_interface()
    {
        $this->shouldImplement('Sylius\Component\Promotion\Generator\InstructionInterface');
    }

    function its_amount_should_be_mutable()
    {
        $this->setAmount(500);
        $this->getAmount()->shouldReturn(500);
    }

    function it_should_not_have_amount_by_default()
    {
        $this->getAmount()->shouldReturn(null);
    }
    
    function it_should_not_have_usage_limit_by_default()
    {
        $this->getUsageLimit()->shouldReturn(null);
    }

    function it_should_not_have_code_length_by_default()
    {
        $this->getCodeLength()->shouldReturn(null);
    }

    function its_usage_limit_should_be_mutable()
    {
        $this->setUsageLimit(3);
        $this->getUsageLimit()->shouldReturn(3);
    }

    function its_code_length_should_be_mutable()
    {
        $this->setCodeLength(5);
        $this->getCodeLength()->shouldReturn(5);
    }
}
