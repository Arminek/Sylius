<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Test\Services;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class RandomInvoiceNumberGeneratorSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Test\Services\RandomInvoiceNumberGenerator');
    }

    public function it_is_invoice_number_generator()
    {
        $this->shouldImplement('Sylius\Bundle\CoreBundle\Checkout\InvoiceNumberGeneratorInterface');
    }

    public function it_generate_random_invoice_number(OrderInterface $order, PaymentInterface $payment)
    {
        $this->generate($order, $payment)->shouldBeString();
    }
}
