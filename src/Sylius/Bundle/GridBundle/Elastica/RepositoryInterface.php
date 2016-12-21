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

use Elastica\Query\AbstractQuery;
use Elastica\QueryBuilder;
use Elastica\QueryBuilder\Version;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface RepositoryInterface
{
    /**
     * @param AbstractQuery|null $query
     *
     * @return AbstractQuery
     */
    public function createQuery(AbstractQuery $query = null);

    /**
     * @param Version|null $version
     *
     * @return QueryBuilder
     */
    public function createQueryBuilder(Version $version = null);
}
