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

namespace Sylius\Bundle\CoreBundle\Command;

use Sylius\Bundle\CoreBundle\Entity\Job;
use SyliusLabs\Polyfill\Symfony\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class RegisterJobsCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'sylius:batch_processing:register_jobs';

    protected function configure(): void
    {
        $this
            ->setDescription('Sylius batch processing register jobs')
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> command allows user to add new jobs for batch processes.
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $container = $this->getContainer();
        $repository = $container->get('sylius.repository.batch_processing_job');
        $registry = $container->get('sylius.batch_processing.worker.registry');
        foreach ($registry->all() as $workerId => $name) {
            $job = (new Job())->setWorkerId($workerId)->setName($name);
            $repository->add($job);
        }

        return 0;
    }
}
