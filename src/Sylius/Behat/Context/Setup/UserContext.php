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

use Sylius\Behat\Context\SetupContext;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class UserContext extends SetupContext
{
    /**
     * @var FactoryInterface
     */
    private $userFactory;

    /**
     * @var FactoryInterface
     */
    private $customerFactory;

    /**
     * @param RepositoryInterface $entityRepository
     * @param SharedStorageInterface $clipboard
     * @param FactoryInterface $userFactory
     * @param FactoryInterface $customerFactory
     */
    public function __construct(
        RepositoryInterface $entityRepository,
        SharedStorageInterface $clipboard,
        FactoryInterface $userFactory,
        FactoryInterface $customerFactory
    ) {
        parent::__construct($entityRepository, $clipboard);

        $this->userFactory = $userFactory;
        $this->customerFactory = $customerFactory;
    }

    /**
     * @Given there is user :email identified by :password
     */
    public function thereIsUserIdentifiedBy($email, $password)
    {
        /** @var UserInterface $user */
        $user = $this->userFactory->createNew();
        $customer = $this->customerFactory->createNew();
        $customer->setFirstName('John');
        $customer->setLastName('Doe');

        $user->setCustomer($customer);
        $user->setUsername('John Doe');
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->addRole('ROLE_USER');

        $this->clipboard->setCurrentResource('user', $user);
        $this->entityRepository->add($user);
    }
}
