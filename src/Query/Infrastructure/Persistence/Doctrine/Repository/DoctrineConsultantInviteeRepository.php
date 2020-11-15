<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultantInvitationRepository,
    Domain\Model\Firm\Program\Consultant\ConsultantInvitee
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineConsultantInviteeRepository extends EntityRepository implements ConsultantInvitationRepository
{

    public function allInvitationsForConsultant(
            string $firmId, string $personnelId, string $consultantId, int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
            "consultantId" => $consultantId,
        ];
        
        $qb = $this->createQueryBuilder("consultantInvitation");
        $qb->select("consultantInvitation")
                ->leftJoin("consultantInvitation.consultant", "consultant")
                ->andWhere($qb->expr()->eq("consultant.id", ":consultantId"))
                ->leftJoin("consultant.personnel", "personnel")
                ->andWhere($qb->expr()->eq("personnel.id", ":personnelId"))
                ->leftJoin("personnel.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
        
    }

    public function anInvitationForConsultant(string $firmId, string $personnelId, string $invitationId): ConsultantInvitee
    {
        $params = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
            "invitationId" => $invitationId,
        ];
        
        $qb = $this->createQueryBuilder("consultantInvitation");
        $qb->select("consultantInvitation")
                ->andWhere($qb->expr()->eq("consultantInvitation.id", ":invitationId"))
                ->leftJoin("consultantInvitation.consultant", "consultant")
                ->leftJoin("consultant.personnel", "personnel")
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
