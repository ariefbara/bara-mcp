<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Query\ {
    Application\Service\Firm\Client\TeamMembership\ProgramParticipation\ActivityLogRepository,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest\ConsultationRequestActivityLog,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession\ConsultationSessionActivityLog,
    Domain\Model\Firm\Team\Member,
    Domain\Model\Firm\Team\TeamProgramParticipation
};
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineActivityLogRepository extends EntityRepository implements ActivityLogRepository
{

    public function allActivityLogsBelongsToTeamParticipantWhereClientIsMember(
            string $firmId, string $clientId, string $teamMembershipId, string $teamProgramParticipationId, int $page,
            int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
            "clientId" => $clientId,
            "teamMembershipId" => $teamMembershipId,
            "teamProgramParticipationId" => $teamProgramParticipationId,
        ];

        $qb = $this->createQueryBuilder("activityLog");
        $qb->select("activityLog")
                ->andWhere($qb->expr()->orX(
                        $qb->expr()->in("activityLog.id", $this->getConsultationRequestActivityDQL()),
                        $qb->expr()->in("activityLog.id", $this->getConsultationSessionActivityDQL())
                ))
                ->orderBy("activityLog.occuredTime", "DESC")
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }
    protected function getConsultationRequestActivityDQL()
    {
        $teamQb = $this->getEntityManager()->createQueryBuilder();
        $teamQb->select("t_cr_team.id")
                ->from(Member::class, "cr_teamMembership")
                ->andWhere($teamQb->expr()->eq("cr_teamMembership.id", ":teamMembershipId"))
                ->leftJoin("cr_teamMembership.team", "t_cr_team")
                ->leftJoin("cr_teamMembership.client", "cr_client")
                ->andWhere($teamQb->expr()->eq("cr_client.id", ":clientId"))
                ->leftJoin("cr_client.firm", "cr_firm")
                ->andWhere($teamQb->expr()->eq("cr_firm.id", ":firmId"))
                ->setMaxResults(1);

        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("cr_programParticipation.id")
                ->from(TeamProgramParticipation::class, "cr_teamProgramParticipation")
                ->andWhere($participantQb->expr()->eq("cr_teamProgramParticipation.id", ":teamProgramParticipationId"))
                ->leftJoin("cr_teamProgramParticipation.programParticipation", "cr_programParticipation")
                ->leftJoin("cr_teamProgramParticipation.team", "cr_team")
                ->andWhere($participantQb->expr()->in("cr_team.id", $teamQb->getDQL()))
                ->setMaxResults(1);

        $consultationRequestActivityLogQb = $this->getEntityManager()->createQueryBuilder();
        $consultationRequestActivityLogQb->select("cr_activityLog.id")
                ->from(ConsultationRequestActivityLog::class, "consultationRequestActivityLog")
                ->leftJoin("consultationRequestActivityLog.activityLog", "cr_activityLog")
                ->leftJoin("consultationRequestActivityLog.consultationRequest", "consultationRequest")
                ->leftJoin("consultationRequest.participant", "cr_participant")
                ->andWhere($consultationRequestActivityLogQb->expr()->in("cr_participant.id", $participantQb->getDQL()));
        
        return $consultationRequestActivityLogQb->getDQL();
    }
    protected function getConsultationSessionActivityDQL()
    {
        $teamQb = $this->getEntityManager()->createQueryBuilder();
        $teamQb->select("t_cs_team.id")
                ->from(Member::class, "cs_teamMembership")
                ->andWhere($teamQb->expr()->eq("cs_teamMembership.id", ":teamMembershipId"))
                ->leftJoin("cs_teamMembership.team", "t_cs_team")
                ->leftJoin("cs_teamMembership.client", "cs_client")
                ->andWhere($teamQb->expr()->eq("cs_client.id", ":clientId"))
                ->leftJoin("cs_client.firm", "cs_firm")
                ->andWhere($teamQb->expr()->eq("cs_firm.id", ":firmId"))
                ->setMaxResults(1);

        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("cs_programParticipation.id")
                ->from(TeamProgramParticipation::class, "cs_teamProgramParticipation")
                ->andWhere($participantQb->expr()->eq("cs_teamProgramParticipation.id", ":teamProgramParticipationId"))
                ->leftJoin("cs_teamProgramParticipation.programParticipation", "cs_programParticipation")
                ->leftJoin("cs_teamProgramParticipation.team", "cs_team")
                ->andWhere($participantQb->expr()->in("cs_team.id", $teamQb->getDQL()))
                ->setMaxResults(1);

        $consultationSessionActivityLogQb = $this->getEntityManager()->createQueryBuilder();
        $consultationSessionActivityLogQb->select("cs_activityLog.id")
                ->from(ConsultationSessionActivityLog::class, "consultationSessionActivityLog")
                ->leftJoin("consultationSessionActivityLog.activityLog", "cs_activityLog")
                ->leftJoin("consultationSessionActivityLog.consultationSession", "consultationSession")
                ->leftJoin("consultationSession.participant", "cs_participant")
                ->andWhere($consultationSessionActivityLogQb->expr()->in("cs_participant.id", $participantQb->getDQL()));
        
        return $consultationSessionActivityLogQb->getDQL();
    }

}
