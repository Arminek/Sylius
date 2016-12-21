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
use Elastica\QueryBuilder as BaseQueryBuilder;
use Elastica\QueryBuilder\Version;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class QueryBuilder
{
    /**
     * @var BaseQueryBuilder
     */
    private $queryBuilder;

    /**
     * @var Query
     */
    private $query;

    /**
     * @param Version|null $version
     */
    private function __construct(Version $version = null)
    {
        $this->queryBuilder = new BaseQueryBuilder($version);
        $this->query = new Query();
    }

    /**
     * @param Version $version
     *
     * @return QueryBuilder
     */
    public function createQueryBuilderWithVersion(Version $version = null)
    {
        return new self($version);
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function query()
    {
        $this->query->setQuery(

        );

        return $this;
    }

    public function filter()
    {
        return $this;
    }

    public function aggregation()
    {
        return $this;
    }

    public function suggest()
    {
        return $this;
    }
}
