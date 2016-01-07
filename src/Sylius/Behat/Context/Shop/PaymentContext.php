<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Shop;

use Sylius\Behat\Context\FeatureContext;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Channel\Model\ChannelInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class PaymentContext extends FeatureContext
{
    /**
     * @Given store allows paying offline
     */
    public function storeAllowsPayingOffline()
    {
        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $this->getService('sylius.factory.payment_method')->createNew();
        $paymentMethod->setCode('PM1');
        $paymentMethod->setGateway('offline');
        $paymentMethod->setName('Offline');
        $paymentMethod->setDescription('Offline payment method');
        $paymentMethod->setFeeCalculatorConfiguration(array('amount' => 10));

        /** @var ChannelInterface $channel */
        $channel = $this->clipboard->getCurrentObject('channel');
        $channel->addPaymentMethod($paymentMethod);

        $this->persistObject($channel);
        $this->persistObject($paymentMethod);
        $this->flushEntityManager();
    }
}
