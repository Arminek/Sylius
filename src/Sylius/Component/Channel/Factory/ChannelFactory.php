<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Sylius\Component\Channel\Factory;

use Sylius\Component\Resource\Factory\Factory;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class ChannelFactory extends Factory implements ChannelFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createNamed($name)
    {
        $channel = $this->createNew();
        $channel->setName($name);

        return $channel;
    }
}
