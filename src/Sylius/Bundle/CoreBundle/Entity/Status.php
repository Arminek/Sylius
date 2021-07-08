<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Entity;

interface Status
{
    public const NOT_ACTIVE = 'not_active';
    public const TODO = 'todo';
    public const IN_PROGRESS = 'in_progress';
    public const FINISHED = 'finished';
    public const FAILED = 'failed';
}
