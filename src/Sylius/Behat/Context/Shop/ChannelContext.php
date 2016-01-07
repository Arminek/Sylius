<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Shop;

use Sylius\Behat\Context\FeatureContext;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberCountry;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class ChannelContext extends FeatureContext
{
    /**
     * @Transform /^channel "([^"]+)"$/
     * @Transform /^"([^"]+)" channel$/
     * @Transform :channel
     */
    public function castChannelNameToChannel($channelName)
    {
        return $this->getService('sylius.factory.channel')->createNamed($channelName);
    }

    /**
     * @Given /that store is operating on the ("[^"]+" channel)/
     */
    public function thatStoreIsOperatingOnTheUnitedStatesChannel(ChannelInterface $channel)
    {
        $channel->setCode('WEB-US');

        /** @var CountryInterface $country */
        $country = $this->getService('sylius.factory.country')->createNew();
        $country->setIsoName('US');
        $country->setEnabled(true);

        /** @var ZoneInterface $zone */
        $zone = $this->getService('sylius.factory.zone')->createNew();
        $zone->setName('USA');
        $zone->setType('country');

        /** @var ZoneMemberCountry $zoneMember */
        $zoneMember = $this->getService('sylius.factory.zone_member_country')->createNew();
        $zoneMember->setCountry($country);
        $zoneMember->setBelongsTo($zone);

        $zone->addMember($zoneMember);

        $this->clipboard->setCurrentObject($channel);
        $this->clipboard->setCurrentObject($country);
        $this->clipboard->setCurrentObject($zoneMember);
        $this->clipboard->setCurrentObject($zone);

        $this->persistObject($channel);
        $this->flushEntityManager();
    }
}
