<?php

namespace User\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Resources\Exception\RegularException;
use User\{
    Application\Service\Manager\ManagerRepository,
    Domain\Model\Manager
};

class DoctrineManagerRepository extends EntityRepository implements ManagerRepository
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

}
