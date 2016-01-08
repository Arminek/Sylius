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

use Behat\Mink\Session;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Bundle\UserBundle\UserEvents;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SecurityServiceSpec extends ObjectBehavior
{
    function let(
        UserRepositoryInterface $userRepository,
        SessionInterface $session
    ) {
        $this->beConstructedWith($userRepository, $session);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Test\Services\SecurityService');
    }

    function it_log_user_in(
        $userRepository,
        $session,
        UserInterface $user,
        Session $minkSession
    ) {
        $userRoles = ['ROLE_USER'];
        $userRepository->findOneBy(array('username' => 'sylius@example.com'))->willReturn($user);
        $user->getRoles()->willReturn($userRoles);
        $user->getPassword()->willReturn('xyz');

        /** @var TokenStorageInterface $tokenStorage */
        $token = new UsernamePasswordToken($user, 'xyz', 'default', $userRoles);
        $session->set('_security_user', serialize($token))->shouldBeCalled();
        $session->save()->shouldBeCalled();
        $session->getName()->willReturn('MOCKEDSID');
        $session->getId()->willReturn('xyzc123');

        $minkSession->setCookie('MOCKEDSID', 'xyzc123')->shouldBeCalled();

        $this->logIn('sylius@example.com', 'default', $minkSession);
    }
}
