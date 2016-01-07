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
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class CurrencyContext extends FeatureContext
{
    /**
     * @Given default currency is USD
     */
    public function defaultCurrencyIsUsd()
    {
        /** @var ChannelInterface $channel */
        $channel = $this->clipboard->getCurrentObject('channel');
        /** @var CurrencyInterface $currency */
        $currency = $this->getService('sylius.factory.currency')->createNew();
        $currency->setCode('USD');
        $currency->setExchangeRate(1.3);
        $currency->enable();

        $currency2 = $this->getService('sylius.factory.currency')->createNew();
        $currency2->setCode('EUR');
        $currency2->setExchangeRate(1.5);
        $currency2->enable();

        $channel->setDefaultCurrency($currency);

        $this->persistObject($currency);
        $this->persistObject($channel);
        $this->persistObject($currency2);
        $this->flushEntityManager();
    }
}
