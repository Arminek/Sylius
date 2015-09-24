<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Sylius\Component\Cart\Model\CartInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class RefreshCartListener
{
    /**
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $entityManager = $args->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof CartInterface) {
                $this->clearAdjustmentsOnEmptyCart($entity);
                $this->refreshCart($entity);
            }
        }
    }

    /**
     * @param CartInterface $cart
     */
    private function refreshCart(CartInterface $cart)
    {
        $cart->calculateTotal();
    }

    /**
     * @param CartInterface $cart
     */
    private function clearAdjustmentsOnEmptyCart(CartInterface $cart)
    {
        if ($cart->isEmpty()) {
            $cart->clearAdjustments();
        }
    }
}
