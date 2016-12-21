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
use FOS\ElasticaBundle\Paginator\FantaPaginatorAdapter;
use FOS\ElasticaBundle\Repository;
use Pagerfanta\Pagerfanta;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Parameters;

/**
 * @author Nicolas Adler <nicolas.adler@openizi.com>
 */
final class DataSource implements DataSourceInterface
{
    /**
     * @var ExpressionBuilder
     */
    private $expressionBuilder;

    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var AbstractQuery
     */
    private $query;

    /**
     * @param Query $query
     * @param Repository $repository
     * @param QueryBuilder $queryBuilder
     */
    public function __construct(Query $query, Repository $repository, QueryBuilder $queryBuilder)
    {
        $this->query = Query::create($query);
        $this->expressionBuilder = new ExpressionBuilder($this->query, $queryBuilder);
        $this->queryBuilder = $queryBuilder;
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function restrict($expression, $condition = DataSourceInterface::CONDITION_AND)
    {
        /** @var ExpressionBuilder $expression */
        switch ($condition) {
            case DataSourceInterface::CONDITION_AND:
                $expression->andX($this->query);
                break;
            case DataSourceInterface::CONDITION_OR:
                $expression->orX($this->query);
                break;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExpressionBuilder()
    {
        return $this->expressionBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(Parameters $parameters)
    {
        $query = Query::create($this->query);

        $paginator = new Pagerfanta(
            new FantaPaginatorAdapter(
                $this->repository->createPaginatorAdapter($query)
            )
        );

        $paginator->setNormalizeOutOfRangePages(true);
        $paginator->setCurrentPage($parameters->get('page', 1));
        $paginator->getCurrentPageResults();

        return $paginator;
    }
}
