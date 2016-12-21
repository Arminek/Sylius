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

use Elastica\Query;
use Elastica\Query\AbstractQuery;
use Elastica\QueryBuilder;
use Elastica\QueryBuilder\Version;
use FOS\ElasticaBundle\Repository;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class ElasticaRepository extends Repository implements RepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createQuery(AbstractQuery $query = null)
    {
        return new Query($query);
    }

    /**
     * {@inheritdoc}
     */
    public function createQueryBuilder(Version $version = null)
    {
        return new QueryBuilder($version);
    }
}
