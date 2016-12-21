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
use Elastica\QueryBuilder;
use Sylius\Component\Grid\Data\ExpressionBuilderInterface;

/**
 * @author Nicolas Adler <nicolas.adler@openizi.com>
 */
class ExpressionBuilder implements ExpressionBuilderInterface
{
    /**
     * @var Query
     */
    private $query;

    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    public function __construct(Query $query, QueryBuilder $queryBuilder)
    {
        $this->query = $query;
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function andX(...$expressions)
    {
        $boolQuery = $this->queryBuilder->query()->bool();
        /** @var Query $expression */
        foreach ($expressions as $expression) {
            $boolQuery->addMust($expression->getQuery());
        }

        $this->query->setQuery($boolQuery);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function orX(...$expressions)
    {
        $boolQuery = $this->queryBuilder->query()->bool();
        /** @var Query $expression */
        foreach ($expressions as $expression) {
            $boolQuery->addShould($expression->getQuery());
        }

        $this->query->setQuery($boolQuery);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function comparison($field, $operator, $value)
    {
        throw new \BadMethodCallException('Not supported yet.');
    }

    /**
     * {@inheritdoc}
     */
    public function equals($field, $value)
    {
        $match = $this->queryBuilder->query()->match($field, $value);

        $this->query->setQuery(
            $match
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function notEquals($field, $value)
    {
        $this->query->setQuery(
            $this->queryBuilder->query()->bool()->addMustNot(
                $this->queryBuilder->query()->match($field, $value)
            )
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function lessThan($field, $value)
    {
        throw new \BadMethodCallException('Not supported yet.');
    }

    /**
     * {@inheritdoc}
     */
    public function lessThanOrEqual($field, $value)
    {
        throw new \BadMethodCallException('Not supported yet.');
    }

    /**
     * {@inheritdoc}
     */
    public function greaterThan($field, $value)
    {
        throw new \BadMethodCallException('Not supported yet.');
    }

    /**
     * {@inheritdoc}
     */
    public function greaterThanOrEqual($field, $value)
    {
        throw new \BadMethodCallException('Not supported yet.');
    }

    /**
     * {@inheritdoc}
     */
    public function in($field, array $values)
    {
        $boolQuery = $this->queryBuilder->query()->bool();
        foreach ($values as $value) {
            $boolQuery->addMust(
                $this->queryBuilder->query()->match($field, $value)
            );
        }

        $this->query->setQuery($boolQuery);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function notIn($field, array $values)
    {
        $boolQuery = $this->queryBuilder->query()->bool();
        foreach ($values as $value) {
            $boolQuery->addMustNot(
                $this->queryBuilder->query()->match($field, $value)
            );
        }

        $this->query->setQuery($boolQuery);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isNull($field)
    {
        $this->query->setQuery(
            $this->queryBuilder->query()->filtered(
                $this->query,
                $this->queryBuilder->filter()->bool_not(
                    $this->queryBuilder->filter()->exists($field)
                )
            )
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isNotNull($field)
    {
        $this->query->setQuery(
            $this->queryBuilder->query()->filtered(
                $this->query,
                $this->queryBuilder->filter()->exists($field)
            )
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function like($field, $pattern)
    {
        $pattern = str_replace('%', '*', $pattern);
        $this->query->setQuery(
            $this->queryBuilder->query()->regexp($field, $pattern)
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function notLike($field, $pattern)
    {
        $pattern = str_replace('%', '*', $pattern);
        $this->query->setQuery(
            $this->queryBuilder->query()->bool()->addMustNot(
                $this->queryBuilder->query()->regexp($field, $pattern)
            )
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function orderBy($field, $direction)
    {
        $this->addOrderBy($field, $direction);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addOrderBy($field, $direction)
    {
        $this->query->addSort([$field => ['order' => $direction]]);

        return $this;
    }

    /**
     * @return Query
     */
    public function getQuery()
    {
        return $this->query;
    }
}
