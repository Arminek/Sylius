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

use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SecurityService
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
        SecurityContextInterface $securityContext,
        SessionInterface $session
    ) {
        $this->userRepository = $userRepository;
        $this->securityContext = $securityContext;
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function logIn($email, $firewallName)
    {
        $user = $this->userRepository->findOneBy(array('username' => $email));

        $token = new UsernamePasswordToken($email, null, $firewallName, $user->getRoles());
        $this->securityContext->setToken($token);

        $this->session->set('_security_main', serialize($token));
        $this->session->save();
    }
}
