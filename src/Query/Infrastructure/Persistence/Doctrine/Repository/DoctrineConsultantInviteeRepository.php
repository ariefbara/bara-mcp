<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Application\Service\Firm\Personnel\ProgramConsultant\ConsultantInvitationRepository;
use Query\Domain\Model\Firm\Program\Consultant\ConsultantInvitee;
use Query\Infrastructure\QueryFilter\TimeIntervalFilter;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineConsultantInviteeRepository extends EntityRepository implements ConsultantInvitationRepository
{

    public function allInvitationsForConsultant(
            string $firmId, string $personnelId, string $consultantId, int $page, int $pageSize,
            ?TimeIntervalFilter $timeIntervalFilter)
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
                ->leftJoin('consultantInvitation.invitee', 'invitee')
                ->leftJoin('invitee.activity', 'activity')
                ->orderBy('activity.startEndTime.startDateTime', 'ASC')
                ->setParameters($params);
        
        if (isset($timeIntervalFilter)) {
            if (!is_null($timeIntervalFilter->getFrom())) {
                $qb->andWhere($qb->expr()->gte("activity.startEndTime.startDateTime", ":from"))
                        ->setParameter("from", $timeIntervalFilter->getFrom());
            }
            if (!is_null($timeIntervalFilter->getTo())) {
                $qb->andWhere($qb->expr()->lte("activity.startEndTime.startDateTime", ":to"))
                        ->setParameter("to", $timeIntervalFilter->getTo());
            }
        }

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
