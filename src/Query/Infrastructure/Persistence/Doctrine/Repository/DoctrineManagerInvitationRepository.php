<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Service\Firm\Manager\ManagerInvitationRepository,
    Domain\Model\Firm\Manager\ManagerInvitation
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineManagerInvitationRepository extends EntityRepository implements ManagerInvitationRepository
{

    public function allInvitationsForManager(string $firmId, string $managerId, int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
            "managerId" => $managerId,
        ];

        $qb = $this->createQueryBuilder("managerInvitation");
        $qb->select("managerInvitation")
                ->leftJoin("managerInvitation.manager", "manager")
                ->andWhere($qb->expr()->eq("manager.id", ":managerId"))
                ->leftJoin("manager.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function anInvitationForManager(string $firmId, string $managerId, string $invitationId): ManagerInvitation
    {
        $params = [
            "firmId" => $firmId,
            "managerId" => $managerId,
            "managerInvitationId" => $invitationId,
        ];

        $qb = $this->createQueryBuilder("managerInvitation");
        $qb->select("managerInvitation")
                ->andWhere($qb->expr()->eq("managerInvitation.id", ":managerInvitationId"))
                ->leftJoin("managerInvitation.manager", "manager")
                ->andWhere($qb->expr()->eq("manager.id", ":managerId"))
                ->leftJoin("manager.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: invitation not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
