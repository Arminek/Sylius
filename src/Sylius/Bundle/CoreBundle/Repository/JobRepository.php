<?php

namespace Sylius\Bundle\CoreBundle\Repository;

use Sylius\Bundle\CoreBundle\Entity\Job;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @method Job|null find($id, $lockMode = null, $lockVersion = null)
 * @method Job|null findOneBy(array $criteria, array $orderBy = null)
 * @method Job[]    findAll()
 * @method Job[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobRepository extends EntityRepository
{
    /** @param Job $resource */
    public function add(ResourceInterface $resource): void
    {
        $job = $this->findOneBy(['workerId' => $resource->getWorkerId()]);
        if ($job === null) {
            parent::add($resource);
        }
    }
}
