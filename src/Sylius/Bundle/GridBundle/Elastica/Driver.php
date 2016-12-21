<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\GridBundle\Elastica;

use FOS\ElasticaBundle\Manager\RepositoryManagerInterface;
use Sylius\Component\Grid\Data\DriverInterface;
use Sylius\Component\Grid\Parameters;

/**
 * @author Nicolas Adler <nicolas.adler@openizi.com>
 */
final class Driver implements DriverInterface
{
    const NAME = 'elastica';

    /**
     * @var RepositoryManagerInterface
     */
    private $repositoryManager;

    /**
     * @param RepositoryManagerInterface $repositoryManager
     */
    public function __construct(RepositoryManagerInterface $repositoryManager)
    {
        $this->repositoryManager = $repositoryManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataSource(array $configuration, Parameters $parameters)
    {
        if (!array_key_exists('class', $configuration)) {
            throw new \InvalidArgumentException('"class" must be configured.');
        }

        /** @var RepositoryInterface $repository */
        $repository = $this->repositoryManager->getRepository($configuration['class']);

        if (isset($configuration['repository']['method'])) {
            $method = $configuration['repository']['method'];
            $arguments = isset($configuration['repository']['arguments']) ? array_values($configuration['repository']['arguments']) : [];

            $query = $repository->$method(...$arguments);
        } else {
            $query = $repository->createQuery();
        }

        return new DataSource($query, $repository, $repository->createQueryBuilder());
    }
}
