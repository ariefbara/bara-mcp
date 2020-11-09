<?php

namespace ActivityCreator\Infrastructure\Persistence\Doctrine\Repository;

use ActivityCreator\ {
    Application\Service\Coordinator\CoordinatorActivityRepository,
    Domain\Model\CoordinatorActivity
};
use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Resources\ {
    Exception\RegularException,
    Uuid
};

class DoctrineCoordinatorActivityRepository extends EntityRepository implements CoordinatorActivityRepository
{
    
    public function aCoordinatorActivityBelongsToPersonnel(string $firmId, string $personnelId,
            string $coordinatorActivityId): CoordinatorActivity
    {
        $params = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
            "coordinatorActivityId" => $coordinatorActivityId,
        ];
        
        $qb = $this->createQueryBuilder("coordinatorActivity");
        $qb->select("coordinatorActivity")
                ->andWhere($qb->expr()->eq("coordinatorActivity.id", ":coordinatorActivityId"))
                ->leftJoin("coordinatorActivity.coordinator", "coordinator")
                ->leftJoin("coordinator.personnel", "personnel")
                ->andWhere($qb->expr()->eq("personnel.id", ":personnelId"))
                ->andWhere($qb->expr()->eq("personnel.firmId", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: coordinator activity not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function add(CoordinatorActivity $coordinatorActivity): void
    {
        $em = $this->getEntityManager();
        $em->persist($coordinatorActivity);
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
