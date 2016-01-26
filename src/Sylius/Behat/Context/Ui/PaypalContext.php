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

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\External\PaypalPage;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class PaypalContext implements Context
{
    /**
     * @var PaypalPage
     */
    private $paypalPage;

    /**
     * @var string
     */
    private $paypalAccountName;

    /**
     * @var string
     */
    private $paypalAccountPassword;

    /**
     * @param PaypalPage $paypalPage
     * @param string $paypalAccountName
     * @param string $paypalAccountPassword
     */
    public function __construct(PaypalPage $paypalPage, $paypalAccountName, $paypalAccountPassword)
    {
        $this->paypalPage = $paypalPage;
        $this->paypalAccountName = $paypalAccountName;
        $this->paypalAccountPassword = $paypalAccountPassword;
    }

    /**
     * @Then I should be redirected to PayPal Express Checkout page
     */
    public function iShouldBeRedirectedToPaypalExpressCheckoutPage()
    {
        $this->paypalPage->verify();
    }

    /**
     * @When I sign in to PayPal and pay successfully
     */
    public function iSignInToPaypalAndPaySuccessfully()
    {
        $this->paypalPage->logIn($this->paypalAccountName, $this->paypalAccountPassword);
        $this->paypalPage->pay();
    }

    /**
     * @When I cancel my PayPal payment
     */
    public function iCancelMyPaypalPayment()
    {
        $this->paypalPage->cancel();
    }
}
