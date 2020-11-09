<?php

namespace ActivityCreator\Infrastructure\Persistence\Doctrine\Repository;

use ActivityCreator\ {
    Application\Service\Manager\ManagerActivityRepository,
    Domain\Model\ManagerActivity
};
use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Resources\ {
    Exception\RegularException,
    Uuid
};

class DoctrineManagerActivityRepository extends EntityRepository implements ManagerActivityRepository
{
    
    public function aManagerActivityOfId(string $firmId, string $managerId, string $managerActivityId): ManagerActivity
    {
        $params = [
            "firmId" => $firmId,
            "managerId" => $managerId,
            "managerActivityId" => $managerActivityId,
        ];
        
        $qb = $this->createQueryBuilder("managerActivity");
        $qb->select("managerActivity")
                ->andWhere($qb->expr()->eq("managerActivity.id", ":managerActivityId"))
                ->leftJoin("managerActivity.manager", "manager")
                ->andWhere($qb->expr()->eq("manager.id", ":managerId"))
                ->andWhere($qb->expr()->eq("manager.firmId", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: manager activity not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function add(ManagerActivity $managerActivity): void
    {
        $em = $this->getEntityManager();
        $em->persist($managerActivity);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
