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

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface SecurityServiceInterface
{
    /**
     * @param string $email
     * @param string $providerKey
     * @param Session $minkSession
     */
    public function logIn($email, $providerKey, Session $minkSession);
}
