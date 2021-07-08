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

namespace Sylius\Bundle\CoreBundle\BatchProcessing\Messages;

final class JobFinished
{
    public int $processId;
    public int $jobId;

    public function __construct(int $processId, int $jobId)
    {
        $this->processId = $processId;
        $this->jobId = $jobId;
    }
}
