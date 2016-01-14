<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\External;

use Sylius\Behat\Page\ExternalPage;
use SensioLabs\Behat\PageObjectExtension\PageObject\Exception\UnexpectedPageException;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class PaypalPage extends ExternalPage
{
    protected $absolutePath = 'https://www.sandbox.paypal.com/pl/cgi-bin/merchantpaymentweb';

    public function logIn($email, $password)
    {
        $this->pressButton('login_button');
        $this->waitFor(10, function() use ($email, $password) {
            return $this->findField('login_email');
        });
        $this->fillField('login_email', $email);
        $this->fillField('login_password', $password);
        $this->pressButton('submitLogin');
    }

    public function pay()
    {
        $this->waitFor(10, function() {
            return $this->findButton('continue');
        });
        $this->pressButton('continue');
    }

    public function cancel()
    {
        $this->waitFor(10, function(){
            return $this->findButton('cancel_return');
        });
        $this->pressButton('cancel_return');
    }

    protected function verifyUrl(array $urlParameters = array())
    {
        if (strpos($this->getSession()->getCurrentUrl(), $this->getUrl($urlParameters))) {
            throw new UnexpectedPageException(sprintf('Expected to be on "%s" but found "%s" instead', $this->getUrl($urlParameters), $this->getSession()->getCurrentUrl()));
        }
    }
}
