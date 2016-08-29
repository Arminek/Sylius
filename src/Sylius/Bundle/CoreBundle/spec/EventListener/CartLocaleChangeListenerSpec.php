<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\EventListener\CartLocaleChangeListener;
use Sylius\Component\Core\Cart\Handler\CartLocaleChangeHandlerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @mixin CartLocaleChangeListener
 *
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class CartLocaleChangeListenerSpec extends ObjectBehavior
{
    function let(CartLocaleChangeHandlerInterface $cartLocaleChangeHandler)
    {
        $this->beConstructedWith($cartLocaleChangeHandler);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CartLocaleChangeListener::class);
    }

    function it_changes_locale_on_the_cart(
        CartLocaleChangeHandlerInterface $cartLocaleChangeHandler,
        GenericEvent $event
    ) {
        $event->getSubject()->willReturn('en_GB');
        $cartLocaleChangeHandler->handle('en_GB')->shouldBeCalled();

        $this->changeCartLocale($event);
    }
}
