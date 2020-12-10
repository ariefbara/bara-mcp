<?php

namespace User\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Resources\Exception\RegularException;
use User\ {
    Application\Service\Personnel\Coordinator\CoordinatorRepository,
    Domain\Model\Personnel\Coordinator
};

class DoctrineCoordinatorRepository extends EntityRepository implements CoordinatorRepository
{
    
    public function aCoordinatorBelongsToPersonnel(string $firmId, string $personnelId, string $coordinatorId): Coordinator
    {
        $params = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
            "coordinatorId" => $coordinatorId,
        ];
        
        $qb = $this->createQueryBuilder("coordinator");
        $qb->select("coordinator")
                ->andWhere($qb->expr()->eq("coordinator", ":coordinatorId"))
                ->leftJoin("coordinator.personnel", "personnel")
                ->andWhere($qb->expr()->eq("personnel", ":personnelId"))
                ->leftJoin("personnel.firm", "firm")
                ->andWhere($qb->expr()->eq("firm", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: coordinator not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
