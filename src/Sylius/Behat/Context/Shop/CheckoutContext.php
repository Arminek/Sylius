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
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Symfony\Cmf\Component\Routing\ChainRouterInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;


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
        $this->clipboard->setCurrentObject($channel);

        $entityManager = $this->getService('doctrine.orm.entity_manager');
        $entityManager->persist($channel);
        $entityManager->flush();
    }

    /**
     * @Given default currency is USD
     */
    public function defaultCurrencyIsUsd()
    {
        /** @var Channel $channel */
        $channel = $this->clipboard->getLatestObject();
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

        $entityManager = $this->getService('sylius.manager.channel');
        $entityManager->persist($currency);
        $entityManager->persist($channel);
        $entityManager->persist($currency2);
        $entityManager->flush();

    }

    /**
     * @Given there is user :email identified by :password
     */
    public function thereIsUserIdentifiedBy($email, $password)
    {
        $entityManager = $this->getService('sylius.manager.user');
        /** @var UserInterface $user */
        $user = $this->getService('sylius.factory.user')->createNew();
        /** @var CustomerInterface $customer */
        $customer = $this->getService('sylius.factory.customer')->createNew();
        $customer->setEmail($email);

        $user->setCustomer($customer);
        $user->setPlainPassword($password);
        $user->addRole('ROLE_USER');

        $this->clipboard->setCurrentObject($user);

        $entityManager->persist($user);
        $entityManager->flush();
    }

    /**
     * @Given catalog has a product :productName priced at $:price
     */
    public function catalogHasAProductPricedAt($productName, $price)
    {
        $entityManager = $this->getService('sylius.manager.product');
        /** @var ProductInterface $product */
        $product = $this->getService('sylius.factory.product')->createNew();
        $product->setName($productName);
        $product->setPrice((int) $price);
        $product->setDescription('Awesome star wars mug');

        $channel = $this->clipboard->getCurrentObject('channel');
        $product->addChannel($channel);

        $entityManager->persist($product);
        $entityManager->flush();
    }

    /**
     * @Given store allows paying offline
     */
    public function storeAllowsPayingOffline()
    {
        $entityManager = $this->getService('sylius.manager.payment');
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

        $entityManager->persist($channel);
        $entityManager->persist($paymentMethod);
        $entityManager->flush();
    }

    /**
     * @Given I am logged in as :email
     */
    public function iAmLoggedInAs($email)
    {
        $driver = $this->getSession()->getDriver();
        $client = $driver->getClient();
        

        $this->getService('sylius.behat.security')->logIn($email, 'main', $session);
    }

    /**
     * @Given I added product :name to cart
     */
    public function iAddedProductToCart($name)
    {
        /** @var ProductInterface $product */
        $product = $this->getService('sylius.repository.product')->findOneBy(array('name' => $name));
        /** @var ChainRouterInterface $router */
        $productShowPage = $this->getPage('Product\ProductShowPage')->open(array('slug' => $product->getSlug()));
        $productShowPage->pressButton('Add to cart');
    }

    /**
     * @When I proceed selecting offline payment method
     */
    public function iProceedSelectingOfflinePaymentMethod()
    {
        /** @var SecurityContextInterface $securityContext */
        $securityContext =  $this->getService('security.context');
        $token = $securityContext->getToken();

        $this->getPage('Checkout\CheckoutPaymentStep')->open();
        $content = $this->getSession()->getPage()->getText();
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
