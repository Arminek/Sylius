<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Component\Core\Cart\Handler\CartLocaleChangeHandlerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class CartLocaleChangeListener
{
    /**
     * @var CartLocaleChangeHandlerInterface
     */
    private $cartLocaleChangeHandler;

    /**
     * @param CartLocaleChangeHandlerInterface $cartLocaleChangeHandler
     */
    public function __construct(CartLocaleChangeHandlerInterface $cartLocaleChangeHandler)
    {
        $this->cartLocaleChangeHandler = $cartLocaleChangeHandler;
    }

    /**
     * @param GenericEvent $event
     */
    public function changeCartLocale(GenericEvent $event)
    {
        $this->cartLocaleChangeHandler->handle($event->getSubject());
    }
}
