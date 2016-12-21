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
use Sylius\Component\Core\Model\TaxonInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ProductRepository extends ElasticaRepository
{
    /**
     * @param TaxonInterface $taxon
     *
     * @return Query
     */
    public function createQueryWithTaxon(TaxonInterface $taxon)
    {
        $query = $this->createQuery();
        $queryBuilder = $this->createQueryBuilder();

        $query->setPostFilter(
            $queryBuilder->filter()->nested()->setPath('productTaxons')->setQuery(
                $queryBuilder->query()->match('productTaxons.taxon.code', $taxon->getCode())
            )
        );

        return $query;
    }
}
