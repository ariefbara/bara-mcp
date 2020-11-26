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
                ->leftJoin("manager.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: manager not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

    public function aManagerInFirmByEmailAndIdentifier(string $firmIdentifier, string $email): Manager
    {
        $params = [
            "firmIdentifier" => $firmIdentifier,
            "email" => $email,
        ];

        $qb = $this->createQueryBuilder("manager");
        $qb->select("manager")
                ->andWhere($qb->expr()->eq("manager.email", ":email"))
                ->leftJoin("manager.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.identifier", ":firmIdentifier"))
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
