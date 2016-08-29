<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Handler;

use Sylius\Component\Cart\Context\CartContextInterface;
use Sylius\Component\Cart\Context\CartNotFoundException;
use Sylius\Component\Cart\SyliusCartEvents;
use Sylius\Component\Core\Cart\Handler\CartLocaleChangeHandlerInterface;
use Sylius\Component\Core\Exception\HandleException;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Intl\Intl;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class CartLocaleChangeHandler implements CartLocaleChangeHandlerInterface
{
    /**
     * @var CartContextInterface
     */
    private $cartContext;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param CartContextInterface $cartContext
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(CartContextInterface $cartContext, EventDispatcherInterface $eventDispatcher)
    {
        $this->cartContext = $cartContext;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($code)
    {
        if (null === Intl::getLocaleBundle()->getLocaleName($code)) {
            throw new HandleException(self::class, sprintf('This "%s" looks like invalid property', $code));
        }

        try {
            /** @var OrderInterface $cart */
            $cart = $this->cartContext->getCart();
        } catch (CartNotFoundException $exception) {
            throw new HandleException(self::class, 'Sylius cannot find cart', $exception);
        }

        $cart->setLocaleCode($code);
        $this->eventDispatcher->dispatch(SyliusCartEvents::CART_CHANGE, new GenericEvent($cart));
    }
}
