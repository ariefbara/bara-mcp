<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Query\ {
    Application\Service\Firm\Client\TeamMembership\ProgramParticipation\ActivityLogRepository,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest\ConsultationRequestActivityLog,
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

        $teamQb = $this->getEntityManager()->createQueryBuilder();
        $teamQb->select("t_team.id")
                ->from(Member::class, "teamMembership")
                ->andWhere($teamQb->expr()->eq("teamMembership.id", ":teamMembershipId"))
                ->leftJoin("teamMembership.team", "t_team")
                ->leftJoin("teamMembership.client", "client")
                ->andWhere($teamQb->expr()->eq("client.id", ":clientId"))
                ->leftJoin("client.firm", "firm")
                ->andWhere($teamQb->expr()->eq("firm.id", ":firmId"))
                ->setMaxResults(1);

        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("programParticipation.id")
                ->from(TeamProgramParticipation::class, "teamProgramParticipation")
                ->andWhere($participantQb->expr()->eq("teamProgramParticipation.id", ":teamProgramParticipationId"))
                ->leftJoin("teamProgramParticipation.programParticipation", "programParticipation")
                ->leftJoin("teamProgramParticipation.team", "team")
                ->andWhere($participantQb->expr()->in("team.id", $teamQb->getDQL()))
                ->setMaxResults(1);

        $consultationRequestActivityLogQb = $this->getEntityManager()->createQueryBuilder();
        $consultationRequestActivityLogQb->select("cr_activityLog.id")
                ->from(ConsultationRequestActivityLog::class, "consultationRequestActivityLog")
                ->leftJoin("consultationRequestActivityLog.activityLog", "cr_activityLog")
                ->leftJoin("consultationRequestActivityLog.consultationRequest", "consultationRequest")
                ->leftJoin("consultationRequest.participant", "cr_participant")
                ->andWhere($consultationRequestActivityLogQb->expr()->in("cr_participant.id", $participantQb->getDQL()));

        $qb = $this->createQueryBuilder("activityLog");
        $qb->select("activityLog")
                ->andWhere($qb->expr()->orX(
                        $qb->expr()->in("activityLog.id", $consultationRequestActivityLogQb->getDQL())
                ))
                ->orderBy("activityLog.occuredTime", "DESC")
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
