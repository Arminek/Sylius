<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\SnippetAcceptingContext;
use Sylius\Behat\Context\FeatureContext;
use Sylius\Component\Core\Model\ShippingMethod;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Shipping\Calculator\DefaultCalculators;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class CheckoutContext extends FeatureContext implements SnippetAcceptingContext
{
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
        $checkoutAddressingPage->fillAddressingDetails(
            'John',
            'Doe',
            'United States',
            '0635 Myron Hollow Apt. 711',
            'North Bridget',
            '93-554',
            '321123456'
        );
        $checkoutAddressingPage->pressButton('Continue');

        $checkoutShippingPage = $this->getPage('Checkout\CheckoutShippingStep');
        $checkoutShippingPage->pressRadio('DHL');
        $checkoutShippingPage->pressButton('Continue');

        $checkoutPaymentPage = $this->getPage('Checkout\CheckoutPaymentStep');
        $checkoutPaymentPage->pressRadio('Offline');
        $checkoutPaymentPage->pressButton('Continue');
    }

    /**
     * @When I confirm my order
     */
    public function iConfirmMyOrder()
    {
        $checkoutFinalizePage = $this->getPage('Checkout\CheckoutFinalizeStep')->open();
        $checkoutFinalizePage->clickLink('Place order');
    }

    /**
     * @Then I should see see the thank you page
     */
    public function iShouldSeeSeeTheThankYouPage()
    {
        throw new PendingException();
    }
}
