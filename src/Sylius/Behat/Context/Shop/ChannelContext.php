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
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberCountry;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class ChannelContext extends FeatureContext
{
    /**
     * @Transform /^channel "([^"]+)"$/
     * @Transform /^"([^"]+)" channel$/
     * @Transform :channel
     */
    public function castChannelNameToChannel($channelName)
    {
        return $this->getService('sylius.factory.channel')->createNamed($channelName);
    }

    /**
     * @Given /that store is operating on the ("[^"]+" channel)/
     */
    public function thatStoreIsOperatingOnTheUnitedStatesChannel(ChannelInterface $channel)
    {
        $channel->setCode('WEB-US');

        /** @var CountryInterface $country */
        $country = $this->getService('sylius.factory.country')->createNew();
        $country->setIsoName('US');
        $country->setEnabled(true);

        /** @var ZoneInterface $zone */
        $zone = $this->getService('sylius.factory.zone')->createNew();
        $zone->setName('USA');
        $zone->setType('country');

        /** @var ZoneMemberCountry $zoneMember */
        $zoneMember = $this->getService('sylius.factory.zone_member_country')->createNew();
        $zoneMember->setCountry($country);
        $zoneMember->setBelongsTo($zone);

        $zone->addMember($zoneMember);

        $this->clipboard->setCurrentObject($channel);
        $this->clipboard->setCurrentObject($country);
        $this->clipboard->setCurrentObject($zoneMember);
        $this->clipboard->setCurrentObject($zone);

        $this->persistObject($channel);
        $this->flushEntityManager();
    }

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
