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

use Behat\Mink\Session;
use SensioLabs\Behat\PageObjectExtension\PageObject\Factory;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;
use Symfony\Cmf\Component\Routing\ChainRouterInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
abstract class SymfonyPage extends Page
{
    /**
     * @var ChainRouterInterface
     */
    protected $router;

    /**
     * @param Session $session
     * @param Factory $factory
     * @param array $parameters
     * @param ChainRouterInterface $router
     */
    public function __construct(Session $session, Factory $factory, array $parameters = array(), ChainRouterInterface $router)
    {
        parent::__construct($session, $factory, $parameters);

        $this->router = $router;
    }

    /**
     * @param string $locator
     */
    public function pressRadio($locator)
    {
        $radio = $this->findField($locator);
        $this->fillField($radio->getAttribute('name'), $radio->getAttribute('value'));
    }

    /**
     * @param array $urlParameters
     *
     * @return string
     */
    protected function getUrl(array $urlParameters = array())
    {
        if (null !== $this->getRouteName()) {
            return $this->router->generate($this->getRouteName(), $urlParameters, true);
        }
    }

    abstract public function getRouteName();
}
