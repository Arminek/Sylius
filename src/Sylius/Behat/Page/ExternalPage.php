<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page;

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;
use SensioLabs\Behat\PageObjectExtension\PageObject\Exception\PathNotProvidedException;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
abstract class ExternalPage extends Page
{
    protected $absolutePath = null;

    public function assertRoute(array $urlParameters = array())
    {
        $this->verify($urlParameters);
    }

    protected function getUrl(array $urlParameters = array())
    {
        if (null === $this->absolutePath) {
            throw new PathNotProvidedException('You must add a absolutePath property to your page object');
        }

        return $this->absolutePath;
    }
}
