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

use Sylius\Behat\Context\SetupContext;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Calculator\DefaultCalculators;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class ChannelContext extends SetupContext
{
    /**
     * @var FactoryInterface
     */
    private $channelFactory;

    /**
     * @var FactoryInterface
     */
    private $currencyFactory;

    /**
     * @var FactoryInterface
     */
    private $paymentMethodFactory;

    /**
     * @var FactoryInterface
     */
    private $zoneFactory;

    /**
     * @var FactoryInterface
     */
    private $zoneMemberFactory;

    /**
     * @var FactoryInterface
     */
    private $countryFactory;

    /**
     * @var FactoryInterface
     */
    private $shippingMethodFactory;

    /**
     * @param RepositoryInterface $entityRepository
     * @param SharedStorageInterface $clipboard
     * @param FactoryInterface $channelFactory
     * @param FactoryInterface $currencyFactory
     * @param FactoryInterface $paymentMethodFactory
     * @param FactoryInterface $zoneFactory
     * @param FactoryInterface $zoneMemberFactory
     * @param FactoryInterface $countryFactory
     * @param FactoryInterface $shippingMethodFactory
     */
    public function __construct(
        RepositoryInterface $entityRepository,
        SharedStorageInterface $clipboard,
        FactoryInterface $channelFactory,
        FactoryInterface $currencyFactory,
        FactoryInterface $paymentMethodFactory,
        FactoryInterface $zoneFactory,
        FactoryInterface $zoneMemberFactory,
        FactoryInterface $countryFactory,
        FactoryInterface $shippingMethodFactory
    ) {
        parent::__construct($entityRepository, $clipboard);

        $this->channelFactory = $channelFactory;
        $this->currencyFactory = $currencyFactory;
        $this->paymentMethodFactory = $paymentMethodFactory;
        $this->zoneFactory = $zoneFactory;
        $this->zoneMemberFactory = $zoneMemberFactory;
        $this->countryFactory = $countryFactory;
        $this->shippingMethodFactory = $shippingMethodFactory;
    }

    /**
     * @Transform /^channel "([^"]+)"$/
     * @Transform /^"([^"]+)" channel$/
     * @Transform :channel
     */
    public function castChannelNameToChannel($channelName)
    {
        return $this->channelFactory->createNamed($channelName);
    }

    /**
     * @Given /that store is operating on the ("[^"]+" channel)/
     */
    public function thatStoreIsOperatingOnTheUnitedStatesChannel(ChannelInterface $channel)
    {
        $this->necessaryStuffToSet();
        $channel->setCode('WEB-US');

        $this->clipboard->setCurrentResource('channel', $channel);
        $this->entityRepository->add($channel);
    }

    /**
     * @Given default currency is :currencyCode
     */
    public function defaultCurrencyIs($currencyCode)
    {
        $currency = $this->currencyFactory->createNew();
        $currency->setCode($currencyCode);
        $currency->setExchangeRate(1.3);
        $channel = $this->clipboard->getCurrentResource('channel');
        $channel->setDefaultCurrency($currency);

        $this->entityRepository->add($currency);
    }

    /**
     * @Given store allows paying offline
     */
    public function storeAllowsPayingOffline()
    {
        $paymentMethod = $this->paymentMethodFactory->createNew();
        $paymentMethod->setCode('PM1');
        $paymentMethod->setGateway('dummy');
        $paymentMethod->setName('Offline');
        $paymentMethod->setDescription('Offline payment method');

        $channel = $this->clipboard->getCurrentResource('channel');
        $channel->addPaymentMethod($paymentMethod);

        $this->entityRepository->add($paymentMethod);
    }

    private function necessaryStuffToSet()
    {
        $country = $this->createCountry('US');
        $zoneMember = $this->createZoneMember('US');
        $zone = $this->createZone('USA', 'United States of America', 'country', $zoneMember);
        $shippingMethod = $this->createShippingMethod('SM1', 'DHL', 'US', array('amount' => 200), DefaultCalculators::PER_ITEM_RATE, $zone);

        $this->entityRepository->add($country);
        $this->entityRepository->add($zone);
        $this->entityRepository->add($zoneMember);
        $this->entityRepository->add($shippingMethod);
    }

    /**
     * @param string $code
     *
     * @return CountryInterface
     */
    private function createCountry($code)
    {
        $country = $this->countryFactory->createNew();
        $country->setCode($code);

        return $country;
    }

    /**
     * @param string $code
     * @param string $name
     * @param string $type
     * @param ZoneMemberInterface $zoneMember
     *
     * @return ZoneInterface
     */
    private function createZone($code, $name, $type, ZoneMemberInterface $zoneMember)
    {
        $zone = $this->zoneFactory->createNew();
        $zone->setCode($code);
        $zone->setName($name);
        $zone->setType($type);
        $zone->addMember($zoneMember);

        return $zone;
    }

    /**
     * @param string $code
     *
     * @return ZoneMemberInterface
     */
    private function createZoneMember($code)
    {
        $zoneMember = $this->zoneMemberFactory->createNew();
        $zoneMember->setCode($code);

        return $zoneMember;
    }

    /**
     * @param string $code
     * @param string $name
     * @param string $currentLocale
     * @param array $calculatorConfiguration
     * @param string $calculator
     * @param ZoneInterface $zone
     *
     * @return ShippingMethodInterface
     */
    private function createShippingMethod(
        $code,
        $name,
        $currentLocale,
        array $calculatorConfiguration,
        $calculator,
        ZoneInterface $zone
    ) {
        $shippingMethod = $this->shippingMethodFactory->createNew();
        $shippingMethod->setEnabled(true);
        $shippingMethod->setCode($code);
        $shippingMethod->setName($name);
        $shippingMethod->setCurrentLocale($currentLocale);
        $shippingMethod->setConfiguration($calculatorConfiguration);
        $shippingMethod->setCalculator($calculator);
        $shippingMethod->setZone($zone);

        return $shippingMethod;
    }
}
