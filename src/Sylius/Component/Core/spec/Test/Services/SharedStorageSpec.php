<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace spec\Sylius\Component\Core\Test\Services;
 
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SharedStorageSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Test\Services\SharedStorage');
    }

    function it_has_objects_in_clipboard(ChannelInterface $channel, ProductInterface $product)
    {
        $this->setCurrentObject($channel);
        $this->getCurrentObject($channel)->shouldReturn($channel);

        $this->setCurrentObject($product);
        $this->getCurrentObject($product)->shouldReturn($product);
    }

    function it_return_latest_added_object(ChannelInterface $channel, ProductInterface $product)
    {
        $this->setCurrentObject($channel);
        $this->setCurrentObject($product);
        $this->getLatestObject()->shouldReturn($product);
    }
}
