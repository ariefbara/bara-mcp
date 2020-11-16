<?php

namespace ActivityInvitee\Infrastructure\Persistence\Doctrine\Repository;

use ActivityInvitee\ {
    Application\Service\Manager\ActivityInvitationRepository,
    Domain\Model\ManagerInvitee
};
use Doctrine\ORM\EntityRepository;

class DoctrineManagerInviteeRepository extends EntityRepository implements ActivityInvitationRepository
{
    
    public function anInvitationBelongsToManager(string $firmId, string $managerId, string $invitationId): ManagerInvitee
    {
        $params = [
            "firmId" => $firmId,
            "managerId" => $managerId,
            "invitationId" => $invitationId,
        ];
        
        $qb = $this->createQueryBuilder("invitation");
        $qb->select("invitation")
                ->andWhere($qb->expr()->eq("invitation.id", ":invitationId"))
                ->leftJoin("invitation.manager", "manager")
                ->andWhere($qb->expr()->eq("manager.id", ":managerId"))
                ->andWhere($qb->expr()->eq("manager.firmId", ":firmId"))
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
