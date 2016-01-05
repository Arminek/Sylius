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

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class CheckoutShippingStep extends Page
{
    /**
     * @var string
     */
    protected $path = '/checkout/shipping';

    /**
     * @param string $locator
     */
    public function pressRadio($locator)
    {
        $radio = $this->findField($locator);
        $this->fillField($radio->getAttribute('name'), $radio->getAttribute('value'));
    }
}
