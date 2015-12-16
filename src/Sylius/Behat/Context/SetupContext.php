<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
abstract class SetupContext implements Context
{
    /**
     * @var RepositoryInterface
     */
    protected $entityRepository;

    /**
     * @var SharedStorageInterface
     */
    protected $clipboard;

    /**
     * @param RepositoryInterface $entityRepository
     * @param SharedStorageInterface $clipboard
     */
    public function __construct(RepositoryInterface $entityRepository, SharedStorageInterface $clipboard)
    {
        $this->entityRepository = $entityRepository;
        $this->clipboard = $clipboard;
    }
}
