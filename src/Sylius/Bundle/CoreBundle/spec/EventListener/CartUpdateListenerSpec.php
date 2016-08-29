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

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\EventListener\CartUpdateListener;
use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @mixin CartUpdateListener
 *
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class CartUpdateListenerSpec extends ObjectBehavior
{
    function let(ObjectManager $cartManager)
    {
        $this->beConstructedWith($cartManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CartUpdateListener::class);
    }

    function it_updates_order(ObjectManager $cartManager, GenericEvent $event, CartInterface $cart)
    {
        $event->getSubject()->willReturn($cart);
        $cartManager->persist($cart)->shouldBeCalled();
        $cartManager->flush()->shouldBeCalled();

        $this->updateCart($event);
    }

    function it_throws_invalid_argument_exception_if_given_subject_is_not_a_cart(
        ObjectManager $cartManager,
        GenericEvent $event,
        CustomerInterface $customer
    ) {
        $event->getSubject()->willReturn($customer);
        $cartManager->persist(Argument::any())->shouldNotBeCalled();
        $cartManager->flush()->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->during('updateCart', [$event]);
    }
}
