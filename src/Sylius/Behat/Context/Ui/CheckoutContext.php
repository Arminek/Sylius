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

use Sylius\Behat\Context\FeatureContext;
use Sylius\Component\Core\Model\ShippingMethod;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Shipping\Calculator\DefaultCalculators;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class CheckoutContext extends FeatureContext
{
    /**
     * @Given I added product :productName to the cart
     */
    public function iAddedProductToCart($productName)
    {
        /** @var ProductInterface $product */
        $product = $this->getService('sylius.repository.product')->findOneBy(array('name' => $productName));

        $productShowPage = $this->getPage('Product\ProductShowPage')->openSpecificProductPage($product);
        $productShowPage->pressButton('Add to cart');
    }

    /**
     * @When I proceed selecting :paymentMethodName payment method
     */
    public function iProceedSelectingPaymentMethod($paymentMethodName)
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

        $this->entityManager->persist($shippingMethod);
        $this->entityManager->persist($zoneMember);
        $this->entityManager->persist($country);
        $this->entityManager->persist($zone);
        $this->entityManager->flush();

        $checkoutAddressingPage = $this->getPage('Checkout\CheckoutAddressingStep')->open();
        $addressingDetails = array(
            'firstName' => 'John',
            'lastName' => 'Doe',
            'country' => 'United States',
            'street' => '0635 Myron Hollow Apt. 711',
            'city' => 'North Bridget',
            'postcode' => '93-554',
            'phoneNumber' => '321123456'
        );
        $checkoutAddressingPage->fillAddressingDetails($addressingDetails);
        $checkoutAddressingPage->pressButton('Continue');

        $checkoutShippingPage = $this->getPage('Checkout\CheckoutShippingStep');
        $checkoutShippingPage->assertRoute();
        $checkoutShippingPage->pressRadio('DHL');
        $checkoutShippingPage->pressButton('Continue');

        $checkoutPaymentPage = $this->getPage('Checkout\CheckoutPaymentStep');
        $checkoutPaymentPage->assertRoute();
        $checkoutPaymentPage->pressRadio($paymentMethodName);
        $checkoutPaymentPage->pressButton('Continue');
    }

    /**
     * @When I confirm my order
     */
    public function iConfirmMyOrder()
    {
        $checkoutFinalizePage = $this->getPage('Checkout\CheckoutFinalizeStep');
        $checkoutFinalizePage->assertRoute();
        $checkoutFinalizePage->clickLink('Place order');
    }

    /**
     * @Then I should see see the thank you page
     */
    public function iShouldSeeSeeTheThankYouPage()
    {
        $user = $this->clipboard->getCurrentObject('User');
        $order = $this->getService('sylius.repository.order')->findOneBy(array('customer' => $user->getCustomer()));
        $thankYouPage = $this->getPage('Checkout\CheckoutThankYouPage');
        $thankYouPage->assertRoute(array('id' => $order->getId()));
        $this->assertSession()->pageTextContains(sprintf('Thank you %s', $user->getEmail()));
    }
}
