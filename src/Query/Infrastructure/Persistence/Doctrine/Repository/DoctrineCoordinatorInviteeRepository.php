<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Query\{
    Application\Service\Firm\Personnel\ProgramCoordinator\CoordinatorInvitationRepository,
    Domain\Model\Firm\Program\Coordinator\CoordinatorInvitee
};
use Resources\{
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineCoordinatorInviteeRepository extends EntityRepository implements CoordinatorInvitationRepository
{

    public function allInvitationsForCoordinator(
            string $firmId, string $personnelId, string $coordinatorId, int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
            "coordinatorId" => $coordinatorId,
        ];

        $qb = $this->createQueryBuilder("coordinatorInvitation");
        $qb->select("coordinatorInvitation")
                ->leftJoin("coordinatorInvitation.coordinator", "coordinator")
                ->andWhere($qb->expr()->eq("coordinator.id", ":coordinatorId"))
                ->leftJoin("coordinator.personnel", "personnel")
                ->andWhere($qb->expr()->eq("personnel.id", ":personnelId"))
                ->leftJoin("personnel.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function anInvitationForCoordinator(string $firmId, string $personnelId, string $invitationId): CoordinatorInvitee
    {
        $params = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
            "invitationId" => $invitationId,
        ];

        $qb = $this->createQueryBuilder("coordinatorInvitation");
        $qb->select("coordinatorInvitation")
                ->andWhere($qb->expr()->eq("coordinatorInvitation.id", ":invitationId"))
                ->leftJoin("coordinatorInvitation.coordinator", "coordinator")
                ->leftJoin("coordinator.personnel", "personnel")
                ->andWhere($qb->expr()->eq("personnel.id", ":personnelId"))
                ->leftJoin("personnel.firm", "firm")
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
