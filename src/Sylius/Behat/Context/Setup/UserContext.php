<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Sylius\Behat\Context\FeatureContext;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\UserInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class UserContext extends FeatureContext
{
    /**
     * @Given there is user :email identified by :password
     */
    public function thereIsUserIdentifiedBy($email, $password)
    {
        /** @var UserInterface $user */
        $user = $this->getService('sylius.factory.user')->createNew();
        /** @var CustomerInterface $customer */
        $customer = $this->getService('sylius.factory.customer')->createNew();
        $customer->setEmail($email);

        $user->setCustomer($customer);
        $user->setPlainPassword($password);
        $user->addRole('ROLE_USER');
        $user->addRole('ROLE_ADMIN');

        $this->clipboard->setCurrentObject($user);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * @Given I am logged in as :email
     */
    public function iAmLoggedInAs($email)
    {
        $this->getPage('Shop\HomePage')->open();
        $this->getService('sylius.behat.security')->logIn($email, 'main', $this->getSession());
    }
}
