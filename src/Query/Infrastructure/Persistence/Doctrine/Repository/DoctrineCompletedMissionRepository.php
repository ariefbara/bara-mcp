<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Service\Firm\Program\Participant\CompletedMissionRepository,
    Domain\Model\Firm\Program\Participant\CompletedMission,
    Domain\Model\Firm\Team\Member,
    Domain\Model\Firm\Team\TeamProgramParticipation
};

class DoctrineCompletedMissionRepository extends EntityRepository implements CompletedMissionRepository
{

    public function lastCompletedMissionOfTeamWhereClientIsMember(
            string $firmId, string $clientId, string $teamMembershipId, string $teamProgramParticipationId): ?CompletedMission
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
        
        $qb = $this->createQueryBuilder('completedMission');
        $qb->select("completedMission")
                ->leftJoin("completedMission.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $participantQb->getDQL()))
                ->orderBy("completedMission.completedTime", "DESC")
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            return null;
        }
    }

    public function lastCompletedMissionProgressOfParticipant(string $firmId, string $programId, string $participantId): ?CompletedMission
    {
        $params = [
            "firmId" => $firmId,
            "programId" => $programId,
            "participantId" => $participantId,
        ];
        
        $qb = $this->createQueryBuilder("completedMission")
                ->leftJoin("completedMission.participant", "participant")
                ->andWhere($qb->expr()->eq("participant.id", ":participantId"))
                ->leftJoin("participant.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->leftJoin("program.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->orderBy("completedMission.completedTime", "DESC")
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            return null;
        }
    }

    public function missionProgressOfParticipant(string $firmId, string $programId, string $participantId)
    {
        $statement = <<<_STATEMENT
SELECT 
(
    SELECT COUNT(*)
    FROM 
        CompletedMission
        LEFT JOIN Participant On Participant.id = CompletedMission.Participant_id
        LEFT JOIN Program On Program.id = Participant.Program_id
    WHERE 
        Program.Firm_id = :firmId
        AND Program.id = :programId
        AND Participant.id = :participantId
    
) as completedMission,
(
    SELECT COUNT(*)
    FROM 
        Mission
        LEFT JOIN Program On Program.id = Mission.Program_id
    WHERE 
        Program.Firm_id = :firmId
        AND Program.id = :programId
) as totalMission
_STATEMENT;
        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        $params = [
            "firmId" => $firmId,
            "programId" => $programId,
            "participantId" => $participantId,
        ];
        $query->execute($params);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function missionProgressOfTeamWhereClientIsMember(
            string $firmId, string $clientId, string $teamMembershipId, string $teamProgramParticipationId)
    {
        
    }

}
