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
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SecurityServiceSpec extends ObjectBehavior
{
    function let(
        UserRepositoryInterface $userRepository,
        SecurityContextInterface $securityContext
    ) {
        $this->beConstructedWith($userRepository, $securityContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Test\Services\SecurityService');
    }

    function it_log_user_in(
        $userRepository,
        $securityContext,
        UserInterface $user
    ) {
        $userRoles = ['ROLE_USER'];
        $userRepository->findOneBy(array('username' => 'sylius@example.com'))->willReturn($user);
        $user->getRoles()->willReturn($userRoles);
        $user->getEmail()->willReturn('sylius@example.com');

        /** @var TokenStorageInterface $tokenStorage */
        $token = new UsernamePasswordToken('sylius@example.com', null, 'default', $userRoles);
        $securityContext->setToken($token)->shouldBeCalled();

        $this->logIn('sylius@example.com', 'default');
    }
}
