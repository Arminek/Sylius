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

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Cart\Model\CartInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class CartUpdateListener
{
    /**
     * @var ObjectManager
     */
    private $cartManager;

    /**
     * @param ObjectManager $cartManager
     */
    public function __construct(ObjectManager $cartManager)
    {
        $this->cartManager = $cartManager;
    }

    /**
     * @param GenericEvent $event
     */
    public function updateCart(GenericEvent $event)
    {
        $cart = $event->getSubject();
        Assert::isInstanceOf($cart, CartInterface::class);

        $this->cartManager->persist($cart);
        $this->cartManager->flush();
    }
}
