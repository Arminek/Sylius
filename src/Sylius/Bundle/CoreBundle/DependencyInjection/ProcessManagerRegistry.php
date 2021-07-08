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

namespace Sylius\Bundle\CoreBundle\DependencyInjection;

use Sylius\Bundle\CoreBundle\BatchProcessing\ProcessManager;

final class ProcessManagerRegistry
{
    private array $registry = [];

    public function add(ProcessManager $processManager, string $id): void
    {
        $this->registry[$id] = $processManager;
    }

    public function get(string $id): ProcessManager
    {
        if (!array_key_exists($id, $this->registry)) {
            throw new \InvalidArgumentException(sprintf('Cannot find %s', $id));
        }

        return $this->registry[$id];
    }

    public function all(): array
    {
        return $this->registry;
    }
}
