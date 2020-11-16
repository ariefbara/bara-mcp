<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Query\{
    Application\Service\Firm\Manager\ManagerFileInfoRepository,
    Domain\Model\Firm\Manager\ManagerFileInfo
};
use Resources\Exception\RegularException;

class DoctrineManagerFileInfoRepository extends EntityRepository implements ManagerFileInfoRepository
{

    public function aManagerFileInfoBelongsToManager(string $firmId, string $managerId, string $managerFileInfoId): ManagerFileInfo
    {
        $params = [
            "firmId" => $firmId,
            "managerId" => $managerId,
            "managerFileInfoId" => $managerFileInfoId,
        ];

        $qb = $this->createQueryBuilder("managerFileInfo");
        $qb->select("managerFileInfo")
                ->andWhere($qb->expr()->eq("managerFileInfo.id", ":managerFileInfoId"))
                ->leftJoin("managerFileInfo.manager", "manager")
                ->andWhere($qb->expr()->eq("manager.id", ":managerId"))
                ->leftJoin("manager.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: manager file info not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
