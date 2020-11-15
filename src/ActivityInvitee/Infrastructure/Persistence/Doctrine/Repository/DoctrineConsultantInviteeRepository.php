<?php

namespace ActivityInvitee\Infrastructure\Persistence\Doctrine\Repository;

use ActivityInvitee\ {
    Application\Service\Consultant\ActivityInvitationRepository,
    Domain\Model\ConsultantInvitee
};
use Doctrine\ORM\EntityRepository;

class DoctrineConsultantInviteeRepository extends EntityRepository implements ActivityInvitationRepository
{
    
    public function anInvitationBelongsToPersonnel(string $firmId, string $personnelId, string $invitationId): ConsultantInvitee
    {
        $params = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
            "invitationId" => $invitationId,
        ];
        
        $qb = $this->createQueryBuilder("invitation");
        $qb->select("invitation")
                ->andWhere($qb->expr()->eq("invitation.id", ":invitationId"))
                ->leftJoin("invitation.consultant", "consultant")
                ->leftJoin("consultant.personnel", "personnel")
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
