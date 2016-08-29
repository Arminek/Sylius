<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Handler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Handler\CartLocaleChangeHandler;
use Sylius\Component\Cart\Context\CartContextInterface;
use Sylius\Component\Cart\Context\CartNotFoundException;
use Sylius\Component\Cart\SyliusCartEvents;
use Sylius\Component\Core\Cart\Handler\CartLocaleChangeHandlerInterface;
use Sylius\Component\Core\Exception\HandleException;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @mixin CartLocaleChangeHandler
 *
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class CartLocaleChangeHandlerSpec extends ObjectBehavior
{
    function let(CartContextInterface $cartContext, EventDispatcherInterface $eventDispatcher)
    {
        $this->beConstructedWith($cartContext, $eventDispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CartLocaleChangeHandler::class);
    }

    function it_implements_cart_locale_change_handler_interface()
    {
        $this->shouldImplement(CartLocaleChangeHandlerInterface::class);
    }

    function it_handles_cart_locale_change(
        CartContextInterface $cartContext,
        EventDispatcherInterface $eventDispatcher,
        OrderInterface $cart
    ) {
        $cartContext->getCart()->willReturn($cart);
        $cart->setLocaleCode('en_GB')->shouldBeCalled();
        $eventDispatcher
            ->dispatch(SyliusCartEvents::CART_CHANGE, new GenericEvent($cart->getWrappedObject()))
            ->shouldBeCalled()
        ;

        $this->handle('en_GB');
    }

    function it_throws_handle_exception_if_cannot_find_cart(
        CartContextInterface $cartContext,
        EventDispatcherInterface $eventDispatcher
    ) {
        $cartContext->getCart()->willThrow(CartNotFoundException::class);
        $eventDispatcher->dispatch(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->shouldThrow(HandleException::class)->during('handle', ['en_GB']);
    }

    function it_throws_handle_exception_if_locale_code_is_not_valid(EventDispatcherInterface $eventDispatcher)
    {
        $eventDispatcher->dispatch(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->shouldThrow(HandleException::class)->during('handle', ['xyz']);
    }
}
