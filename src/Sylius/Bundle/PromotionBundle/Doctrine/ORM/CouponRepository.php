<?php

/*
 * This file is part of the Lakion package.
 *
 * (c) Lakion
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class CouponRepository extends EntityRepository
{
    public function findByCodeLength($codeLength)
    {
        $alias = $this->getAlias();
        $query = $this->getCollectionQueryBuilder();

        return $query
            ->select($alias.'code')
            ->where($query->expr()->length('code'))
            ->getQuery()
            ->getResult();
    }
}
