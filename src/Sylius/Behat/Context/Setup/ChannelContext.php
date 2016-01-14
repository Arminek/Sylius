<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Sylius\Behat\Context\FeatureContext;
use Sylius\Component\Core\Model\ChannelInterface;
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

        $zone->addMember($zoneMember);

        $this->clipboard->setCurrentObject($channel);
        $this->clipboard->setCurrentObject($country);
        $this->clipboard->setCurrentObject($zoneMember);
        $this->clipboard->setCurrentObject($zone);

        $this->entityManager->persist($channel);
        $this->entityManager->flush();
    }

    /**
     * @Given default currency is :currencyCode
     */
    public function defaultCurrencyIs($currencyCode)
    {
        /** @var ChannelInterface $channel */
        $channel = $this->clipboard->getCurrentObject('channel');
        /** @var CurrencyInterface $currency */
        $currency = $this->getService('sylius.factory.currency')->createNew();
        $currency->setCode($currencyCode);
        $currency->setExchangeRate(1.3);
        $channel->setDefaultCurrency($currency);

        $this->entityManager->persist($currency);
        $this->entityManager->persist($channel);
        $this->entityManager->flush();
    }

    /**
     * @Given store allows paying :paymentMethodName
     */
    public function storeAllowsPaying($paymentMethodName)
    {
        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $this->getService('sylius.factory.payment_method')->createNew();
        $paymentMethod->setCode('PM1');
        $paymentMethod->setGateway(strtolower(str_replace(' ', '_', $paymentMethodName)));
//        $paymentMethod->setGateway('dummy');
        $paymentMethod->setName($paymentMethodName);
        $paymentMethod->setDescription('Offline payment method');
        $paymentMethod->setFeeCalculatorConfiguration(array('amount' => 10));

        /** @var ChannelInterface $channel */
        $channel = $this->clipboard->getCurrentObject('channel');
        $channel->addPaymentMethod($paymentMethod);

        $this->entityManager->persist($channel);
        $this->entityManager->persist($paymentMethod);
        $this->entityManager->flush();
    }
}
