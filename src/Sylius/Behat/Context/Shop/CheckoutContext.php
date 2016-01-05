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

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\SnippetAcceptingContext;
use Sylius\Behat\Context\FeatureContext;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberCountry;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShippingMethod;
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Shipping\Calculator\DefaultCalculators;
use Symfony\Cmf\Component\Routing\ChainRouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;


/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class CheckoutContext extends FeatureContext implements SnippetAcceptingContext
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
        /** @var Channel $channel */
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
     * @Given there is user :email identified by :password
     */
    public function thereIsUserIdentifiedBy($email, $password)
    {
        /** @var UserInterface $user */
        $user = $this->getService('sylius.factory.user')->createNew();
        /** @var CustomerInterface $customer */
        $customer = $this->getService('sylius.factory.customer')->createNew();
        $customer->setEmail($email);

        $user->setCustomer($customer);
        $user->setPlainPassword($password);
        $user->addRole('ROLE_USER');
        $user->addRole('ROLE_ADMIN');

        $this->clipboard->setCurrentObject($user);

        $this->persistObject($user);
        $this->flushEntityManager();
    }

    /**
     * @Given catalog has a product :productName priced at $:price
     */
    public function catalogHasAProductPricedAt($productName, $price)
    {
        /** @var ProductInterface $product */
        $product = $this->getService('sylius.factory.product')->createNew();
        $product->setName($productName);
        $product->setPrice((int) $price);
        $product->setDescription('Awesome star wars mug');

        $channel = $this->clipboard->getCurrentObject('channel');
        $product->addChannel($channel);

        $this->persistObject($product);
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
        $paymentMethod->setEnabled(true);
        $paymentMethod->setName('Offline');
        $paymentMethod->setDescription('Offline payment method');

        /** @var ChannelInterface $channel */
        $channel = $this->clipboard->getCurrentObject('channel');
        $channel->addPaymentMethod($paymentMethod);

        $this->persistObject($channel);
        $this->persistObject($paymentMethod);
        $this->flushEntityManager();
    }

    /**
     * @Given I am logged in as :email
     */
    public function iAmLoggedInAs($email)
    {
        $this->getService('sylius.behat.security')->logIn($email, 'main', $this->getSession());
    }

    /**
     * @Given I added product :name to cart
     */
    public function iAddedProductToCart($name)
    {
        /** @var ProductInterface $product */
        $product = $this->getService('sylius.repository.product')->findOneBy(array('name' => $name));

        $productShowPage = $this->getPage('Product\ProductShowPage')->openSpecificProductPage($product);
        $productShowPage->pressButton('Add to cart');
    }

    /**
     * @When I proceed selecting offline payment method
     */
    public function iProceedSelectingOfflinePaymentMethod()
    {
        $zone = $this->clipboard->getCurrentObject('zone');
        $country = $this->clipboard->getCurrentObject('country');
        $zoneMember = $this->clipboard->getCurrentObject('zone_member_country');

        /** @var ShippingMethod $shippingMethod */
        $shippingMethod = $this->getService('sylius.factory.shipping_method')->createNew();
        $shippingMethod->setEnabled(true);
        $shippingMethod->setCode('SM1');
        $shippingMethod->setName('DHL');
        $shippingMethod->setCurrentLocale('US');
        $shippingMethod->setConfiguration(array('amount' => 200));
        $shippingMethod->setCalculator(DefaultCalculators::PER_ITEM_RATE);
        $shippingMethod->setZone($zone);

        $this->persistObject($shippingMethod);
        $this->persistObject($zoneMember);
        $this->persistObject($country);
        $this->persistObject($zone);
        $this->flushEntityManager();

        $checkoutAddressingPage = $this->getPage('Checkout\CheckoutAddressingStep')->open();
        $checkoutAddressingPage->fillField('First name', 'John');
        $checkoutAddressingPage->fillField('Last name', 'Doe');
        $checkoutAddressingPage->selectFieldOption('Country', 'United States');
        $checkoutAddressingPage->fillField('Street', '0635 Myron Hollow Apt. 711');
        $checkoutAddressingPage->fillField('City', 'North Bridget');
        $checkoutAddressingPage->fillField('Postcode', '93-554');
        $checkoutAddressingPage->fillField('Phone number', '321123456');
        $checkoutAddressingPage->pressButton('Continue');
        $checkoutShippingPage = $this->getPage('Checkout\CheckoutShippingStep');
        $checkoutShippingPage->pressRadio('DHL');
        $checkoutShippingPage->pressButton('Continue');
    }

    /**
     * @When I confirm my order
     */
    public function iConfirmMyOrder()
    {
        throw new PendingException();
    }

    /**
     * @Then I should see see the thank you page
     */
    public function iShouldSeeSeeTheThankYouPage()
    {
        throw new PendingException();
    }
}
