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

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\SnippetAcceptingContext;
use Sylius\Behat\Context\FeatureContext;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class InitContext extends FeatureContext implements SnippetAcceptingContext
{
    /**
     * @Given store allows paying with PayPal Checkout Express
     */
    public function storeAllowsPayingWithPaypalCheckoutExpress()
    {
        throw new PendingException();
    }

    /**
     * @When I proceed selecting PayPal Checkout Express payment method
     */
    public function iProceedSelectingPaypalCheckoutExpressPaymentMethod()
    {
        throw new PendingException();
    }

    /**
     * @Then I should be redirected to PayPal Checkout Express page
     */
    public function iShouldBeRedirectedToPaypalCheckoutExpressPage()
    {
        throw new PendingException();
    }

    /**
     * @Given I confirmed my order selecting PayPal Checkout Express payment method
     */
    public function iConfirmedMyOrderSelectingPaypalCheckoutExpressPaymentMethod()
    {
        throw new PendingException();
    }

    /**
     * @Given I am on the PayPal Checkout Express page
     */
    public function iAmOnThePaypalCheckoutExpressPage()
    {
        throw new PendingException();
    }

    /**
     * @When I cancel my PayPal payment
     */
    public function iCancelMyPaypalPayment()
    {
        throw new PendingException();
    }

    /**
     * @Then I should be redirected back to the order payment page
     */
    public function iShouldBeRedirectedBackToTheOrderPaymentPage()
    {
        throw new PendingException();
    }

    /**
     * @Then I should see one cancelled payment and new one ready to be paid
     */
    public function iShouldSeeOneCancelledPaymentAndNewOneReadyToBePaid()
    {
        throw new PendingException();
    }

    /**
     * @When I sign in to PayPal and pay successfully
     */
    public function iSignInToPaypalAndPaySuccessfully()
    {
        throw new PendingException();
    }

    /**
     * @Then I should be redirected back to the thank you page
     */
    public function iShouldBeRedirectedBackToTheThankYouPage()
    {
        throw new PendingException();
    }

    /**
     * @When I sign in to PayPal but fail to pay
     */
    public function iSignInToPaypalButFailToPay()
    {
        throw new PendingException();
    }

    /**
     * @Then I should see one failed payment and new one ready to be paid
     */
    public function iShouldSeeOneFailedPaymentAndNewOneReadyToBePaid()
    {
        throw new PendingException();
    }

    /**
     * @Given I failed to pay
     */
    public function iFailedToPay()
    {
        throw new PendingException();
    }

    /**
     * @Given I am redirected back to the order payment page
     */
    public function iAmRedirectedBackToTheOrderPaymentPage()
    {
        throw new PendingException();
    }

    /**
     * @When I try to pay again
     */
    public function iTryToPayAgain()
    {
        throw new PendingException();
    }

    /**
     * @Then I should see two failed payments and new one ready to be paid
     */
    public function iShouldSeeTwoFailedPaymentsAndNewOneReadyToBePaid()
    {
        throw new PendingException();
    }
}
