<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace spec\Sylius\Component\Channel\Factory;
 
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Model\ChannelInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class ChannelFactorySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Sylius\Component\Channel\Model\Channel');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Channel\Factory\ChannelFactory');
    }

    function it_implements_channel_factory_interface()
    {
        $this->shouldImplement('Sylius\Component\Channel\Factory\ChannelFactoryInterface');
    }

    function it_is_a_factory()
    {
        $this->shouldHaveType('Sylius\Component\Resource\Factory\Factory');
    }

    function it_create_named(ChannelInterface $channel)
    {
        $channel->getDescription()->willReturn(null);
        $channel->getCode()->willReturn(null);
        $channel->getName()->willReturn('United States Webstore');
        $channel->getId()->willReturn(null);
        $channel->getUrl()->willReturn(null);
        $channel->isEnabled()->willReturn(true);
        $channel->getColor()->willReturn(null);
        $channel->getUpdatedAt()->willReturn(null);

        $this->createNamed('United States Webstore')->shouldBeSameAs($channel);
    }

    /**
     * {@inheritdoc}
     */
    public function getMatchers()
    {
        return [
            'beSameAs' => function($subject, $key) {
                if (!$subject instanceof ChannelInterface || !$key instanceof ChannelInterface) {
                    return false;
                }

                return ($subject->getCode() === $key->getCode()
                    && $subject->getColor() === $key->getColor()
                    && $subject->getName() === $key->getName()
                    && $subject->getId() === $key->getId()
                    && $subject->getUrl() === $key->getUrl()
                    && $subject->getDescription() === $key->getDescription()
                    && $subject->isEnabled() === $key->isEnabled()
                    && $subject->getUpdatedAt() === $key->getUpdatedAt()
                );
            }
        ];
    }
}
