<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Sylius\Component\Core\Test\Services;

use Behat\Mink\Session;
use Doctrine\ORM\EntityNotFoundException;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SecurityService implements SecurityServiceInterface
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
        SessionInterface $session
    ) {
        $this->userRepository = $userRepository;
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function logIn($email, $providerKey, Session $minkSession)
    {
        $user = $this->userRepository->findOneBy(array('username' => $email));

        if (null === $user) {
            throw new EntityNotFoundException(sprintf('There is no user with email %s', $email));
        }

        $token = new UsernamePasswordToken($user, $user->getPassword(), $providerKey, $user->getRoles());

        $this->session->set('_security_user', serialize($token));
        $this->session->save();

        $minkSession->setCookie($this->session->getName(), $this->session->getId());
    }
}
