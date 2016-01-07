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

use Sylius\Behat\Context\FeatureContext;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SecurityContext extends FeatureContext
{
    /**
     * @Given I am logged in as :email
     */
    public function iAmLoggedInAs($email)
    {
        $this->getService('sylius.behat.security')->logIn($email, 'main', $this->getSession());
    }
}
