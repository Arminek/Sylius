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
        $this->clipboard->setCurrentObject($channel);
    }

    /**
     * @Given default currency is USD
     */
    public function defaultCurrencyIsUsd()
    {
        $channel = $this->clipboard->getLatestObject();
    }

    /**
     * @Given there is user :arg1 identified by :arg2
     */
    public function thereIsUserIdentifiedBy($arg1, $arg2)
    {
        throw new PendingException();
    }

    /**
     * @Given catalog has a product :arg1 priced at $:arg2
     */
    public function catalogHasAProductPricedAt($arg1, $arg2)
    {
        throw new PendingException();
    }

    /**
     * @Given store allows paying offline
     */
    public function storeAllowsPayingOffline()
    {
        throw new PendingException();
    }

    /**
     * @Given I am logged in as :arg1
     */
    public function iAmLoggedInAs($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given I added product :arg1 to cart
     */
    public function iAddedProductToCart($arg1)
    {
        throw new PendingException();
    }

    /**
     * @When I proceed selecting offline payment method
     */
    public function iProceedSelectingOfflinePaymentMethod()
    {
        throw new PendingException();
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
