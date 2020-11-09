<?php

namespace ActivityCreator\Infrastructure\Persistence\Doctrine\Repository;

use ActivityCreator\{
    Application\Service\Coordinator\CoordinatorRepository,
    Domain\DependencyModel\Firm\Personnel\Coordinator,
    Domain\service\CoordinatorRepository as InterfaceForDomainService
};
use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Resources\Exception\RegularException;

class DoctrineCoordinatorRepository extends EntityRepository implements CoordinatorRepository, InterfaceForDomainService
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
                ->andWhere($qb->expr()->eq("coordinator.id", ":coordinatorId"))
                ->leftJoin("coordinator.personnel", "personnel")
                ->andWhere($qb->expr()->eq("personnel.id", ":personnelId"))
                ->andWhere($qb->expr()->eq("personnel.firmId", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: coordinator not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function ofId(string $coordinatorId): Coordinator
    {
        $coordinator = $this->findOneBy(["id" => $coordinatorId]);
        if (empty($coordinator)) {
            $errorDetail = "not found: coordinator not found";
            throw RegularException::notFound($errorDetail);
        }
        return $coordinator;
    }

}
