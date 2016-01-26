<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Checkout;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Payment\Model\PaymentInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class InvoiceNumberGeneratorBasedOnIdsSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Checkout\InvoiceNumberGeneratorBasedOnIds');
    }

    public function it_is_invoice_number_generator()
    {
        $this->shouldImplement('Sylius\Bundle\CoreBundle\Checkout\InvoiceNumberGeneratorInterface');
    }

    public function it_generate_random_invoice_number(OrderInterface $order, PaymentInterface $payment)
    {
        $order->getId()->willReturn('001');
        $payment->getId()->willReturn('1');

        $this->generate($order, $payment)->shouldReturn('001-1');
    }
}
