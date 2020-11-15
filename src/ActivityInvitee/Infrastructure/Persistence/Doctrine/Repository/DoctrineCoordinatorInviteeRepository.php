<?php

namespace ActivityInvitee\Infrastructure\Persistence\Doctrine\Repository;

use ActivityInvitee\ {
    Application\Service\Coordinator\ActivityInvitationRepository,
    Domain\Model\CoordinatorInvitee
};
use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Resources\Exception\RegularException;

class DoctrineCoordinatorInviteeRepository extends EntityRepository implements ActivityInvitationRepository
{
    
    public function anInvitationBelongsToPersonnel(string $firmId, string $personnelId, string $invitationId): CoordinatorInvitee
    {
        $params = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
            "invitationId" => $invitationId,
        ];
        
        $qb = $this->createQueryBuilder("invitation");
        $qb->select("invitation")
                ->andWhere($qb->expr()->eq("invitation.id", ":invitationId"))
                ->leftJoin("invitation.coordinator", "coordinator")
                ->leftJoin("coordinator.personnel", "personnel")
                ->andWhere($qb->expr()->eq("personnel.id", ":personnelId"))
                ->andWhere($qb->expr()->eq("personnel.firmId", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: invitation not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
