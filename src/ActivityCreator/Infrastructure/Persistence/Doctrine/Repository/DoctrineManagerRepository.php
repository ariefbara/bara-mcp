<?php

namespace ActivityCreator\Infrastructure\Persistence\Doctrine\Repository;

use ActivityCreator\{
    Application\Service\Manager\ManagerRepository,
    Domain\DependencyModel\Firm\Manager,
    Domain\service\ManagerRepository as InterfaceForDomainService
};
use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Resources\Exception\RegularException;

class DoctrineManagerRepository extends EntityRepository implements ManagerRepository, InterfaceForDomainService
{

    public function aManagerInFirm(string $firmId, string $managerId): Manager
    {
        $params = [
            "firmId" => $firmId,
            "managerId" => $managerId,
        ];

        $qb = $this->createQueryBuilder("manager");
        $qb->select("manager")
                ->andWhere($qb->expr()->eq("manager.id", ":managerId"))
                ->andWhere($qb->expr()->eq("manager.firmId", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: manager not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function ofId(string $managerId): Manager
    {
        $manager = $this->findOneBy(["id" => $managerId]);
        if (empty($manager)) {
            $errorDetail = "not found: manager not found";
            throw RegularException::notFound($errorDetail);
        }
        return $manager;
    }

}
