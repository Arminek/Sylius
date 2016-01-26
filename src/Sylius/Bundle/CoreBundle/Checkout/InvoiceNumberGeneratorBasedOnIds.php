<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Checkout;

use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Payment\Model\PaymentInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class InvoiceNumberGeneratorBasedOnIds implements InvoiceNumberGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate(OrderInterface $order, PaymentInterface $payment)
    {
        return $order->getId().'-'.$payment->getId();
    }
}
