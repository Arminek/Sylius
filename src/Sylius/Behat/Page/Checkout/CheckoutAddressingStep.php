<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Checkout;

use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class CheckoutAddressingStep extends SymfonyPage
{
    /**
     * @return string
     */
    public function getRouteName()
    {
        return 'sylius_checkout_addressing';
    }

    public function fillAddressingDetails(
        $firstName,
        $lastName,
        $country,
        $street,
        $city,
        $postcode,
        $phoneNumber
    ) {
        $this->fillField('First name', $firstName);
        $this->fillField('Last name', $lastName);
        $this->selectFieldOption('Country', $country);
        $this->fillField('Street', $street);
        $this->fillField('City', $city);
        $this->fillField('Postcode', $postcode);
        $this->fillField('Phone number', $phoneNumber);
    }
}
