<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use PDO;
use Query\Application\Service\Firm\Program\MissionRepository;
use Query\Application\Service\MissionRepository as InterfaceForGuest;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Program\Mission;
use Query\Domain\Model\Firm\Team\Member;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Model\User\UserParticipant;
use Query\Domain\Task\Dependency\Firm\Program\MissionRepository as MissionRepository2;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineMissionRepository extends EntityRepository implements MissionRepository, InterfaceForGuest, MissionRepository2
{

    public function ofId(string $firmId, string $programId, string $missionId): Mission
    {
        $params = [
            "firmId" => $firmId,
            "programId" => $programId,
            "missionId" => $missionId,
        ];

        $qb = $this->createQueryBuilder("mission");
        $qb->select("mission")
                ->andWhere($qb->expr()->eq("mission.id", ":missionId"))
                ->leftJoin("mission.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->leftJoin("program.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: mission not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function all(string $firmId, string $programId, int $page, int $pageSize, ?bool $publishedOnly = true)
    {
        $params = [
            "firmId" => $firmId,
            "programId" => $programId,
        ];

        $qb = $this->createQueryBuilder("mission");
        $qb->select("mission")
                ->leftJoin("mission.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->leftJoin("program.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params);

        if ($publishedOnly) {
            $qb->andWhere($qb->expr()->eq("mission.published", "true"));
        }

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function aMissionInProgramWhereClientParticipate(
            string $firmId, string $clientId, string $programParticipationId, string $missionId): Mission
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'programParticipationId' => $programParticipationId,
            'missionId' => $missionId,
        ];

        $programQb = $this->getEntityManager()->createQueryBuilder();
        $programQb->select("t_program.id")
                ->from(ClientParticipant::class, "clientParticipant")
                ->andWhere($programQb->expr()->eq('clientParticipant.id', ':programParticipationId'))
                ->leftJoin('clientParticipant.client', 'client')
                ->andWhere($programQb->expr()->eq('client.id', ':clientId'))
                ->leftJoin('client.firm', 'firm')
                ->andWhere($programQb->expr()->eq('firm.id', ':firmId'))
                ->leftJoin('clientParticipant.participant', 'participant')
                ->leftJoin('participant.program', 't_program')
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('mission');
        $qb->select("mission")
                ->andWhere($qb->expr()->eq("mission.id", ":missionId"))
                ->leftJoin("mission.program", "program")
                ->andWhere($qb->expr()->in("program.id", $programQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: mission not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aMissionByPositionInProgramWhereClientParticipate(
            string $firmId, string $clientId, string $programParticipationId, string $missionPosition): Mission
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'programParticipationId' => $programParticipationId,
            'missionPosition' => $missionPosition,
        ];

        $programQb = $this->getEntityManager()->createQueryBuilder();
        $programQb->select("t_program.id")
                ->from(ClientParticipant::class, "clientParticipant")
                ->andWhere($programQb->expr()->eq('clientParticipant.id', ':programParticipationId'))
                ->leftJoin('clientParticipant.client', 'client')
                ->andWhere($programQb->expr()->eq('client.id', ':clientId'))
                ->leftJoin('client.firm', 'firm')
                ->andWhere($programQb->expr()->eq('firm.id', ':firmId'))
                ->leftJoin('clientParticipant.participant', 'participant')
                ->leftJoin('participant.program', 't_program')
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('mission');
        $qb->select("mission")
                ->andWhere($qb->expr()->eq("mission.position", ":missionPosition"))
                ->leftJoin("mission.program", "program")
                ->andWhere($qb->expr()->in("program.id", $programQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: mission not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allMissionsInProgramWhereClientParticipate(
            string $firmId, string $clientId, string $programParticipationId)
    {
        $statement = <<<_STATEMENT
SELECT y.id, y.name, y.description, y.position, z.submittedWorksheet
FROM (
    SELECT Participant.id participantId, Participant.Program_id programId
    FROM ClientParticipant
        LEFT JOIN Participant ON Participant.id = ClientParticipant.Participant_id
        LEFT JOIN Client ON Client.id = ClientParticipant.Client_id
    WHERE Participant.active = true
        AND ClientParticipant.id = :programParticipationId
        AND Client.id = :clientId
        AND Client.Firm_id = :firmId
)x
LEFT JOIN (
    SELECT Mission.id, Mission.name, Mission.description, Mission.position, Mission.Program_id programId
    FROM Mission
    WHERE Mission.published = true
    GROUP BY id
)y ON y.programId = x.programId
LEFT JOIN (
    SELECT 
        Worksheet.Participant_id participantId,
        Worksheet.Mission_id missionId,
        COUNT(Worksheet.id) AS submittedWorksheet
    FROM Worksheet
    WHERE Worksheet.removed = false
    GROUP BY missionId, participantId
)z ON z.missionId = y.id AND z.participantId = x.participantId                
_STATEMENT;
        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        $params = [
            "firmId" => $firmId,
            "clientId" => $clientId,
            "programParticipationId" => $programParticipationId,
        ];
        $query->execute($params);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function aMissionInProgramWhereUserParticipate(
            string $userId, string $programParticipationId, string $missionId): Mission
    {
        $params = [
            'userId' => $userId,
            'programParticipationId' => $programParticipationId,
            'missionId' => $missionId,
        ];

        $programQb = $this->getEntityManager()->createQueryBuilder();
        $programQb->select("t_program.id")
                ->from(UserParticipant::class, "userParticipant")
                ->andWhere($programQb->expr()->eq('userParticipant.id', ':programParticipationId'))
                ->leftJoin('userParticipant.user', 'user')
                ->andWhere($programQb->expr()->eq('user.id', ':userId'))
                ->leftJoin('userParticipant.participant', 'participant')
                ->leftJoin('participant.program', 't_program')
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('mission');
        $qb->select("mission")
                ->andWhere($qb->expr()->eq("mission.id", ":missionId"))
                ->leftJoin("mission.program", "program")
                ->andWhere($qb->expr()->in("program.id", $programQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: mission not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aMissionByPositionInProgramWhereUserParticipate(string $userId, string $programParticipationId,
            string $missionPosition): Mission
    {
        $params = [
            'userId' => $userId,
            'programParticipationId' => $programParticipationId,
            'missionPosition' => $missionPosition,
        ];

        $programQb = $this->getEntityManager()->createQueryBuilder();
        $programQb->select("t_program.id")
                ->from(UserParticipant::class, "userParticipant")
                ->andWhere($programQb->expr()->eq('userParticipant.id', ':programParticipationId'))
                ->leftJoin('userParticipant.user', 'user')
                ->andWhere($programQb->expr()->eq('user.id', ':userId'))
                ->leftJoin('userParticipant.participant', 'participant')
                ->leftJoin('participant.program', 't_program')
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('mission');
        $qb->select("mission")
                ->andWhere($qb->expr()->eq("mission.position", ":missionPosition"))
                ->leftJoin("mission.program", "program")
                ->andWhere($qb->expr()->in("program.id", $programQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: mission not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allMissionsInProgramWhereUserParticipate(string $userId, string $programParticipationId)
    {
        $statement = <<<_STATEMENT
SELECT y.id, y.name, y.description, y.position, z.submittedWorksheet
FROM (
    SELECT Participant.id participantId, Participant.Program_id programId
    FROM UserParticipant
        LEFT JOIN Participant ON Participant.id = UserParticipant.Participant_id
    WHERE Participant.active = true
        AND UserParticipant.id = :programParticipationId
        AND UserParticipant.User_id = :userId
)x
LEFT JOIN (
    SELECT Mission.id, Mission.name, Mission.description, Mission.position, Mission.Program_id programId
    FROM Mission
    WHERE Mission.published = true
    GROUP BY id
)y ON y.programId = x.programId
LEFT JOIN (
    SELECT 
        Worksheet.Participant_id participantId,
        Worksheet.Mission_id missionId,
        COUNT(Worksheet.id) AS submittedWorksheet
    FROM Worksheet
    WHERE Worksheet.removed = false
    GROUP BY missionId, participantId
)z ON z.missionId = y.id AND z.participantId = x.participantId
_STATEMENT;
        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        $params = [
            "userId" => $userId,
            "programParticipationId" => $programParticipationId,
        ];
        $query->execute($params);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function aMissionByPositionInProgramWhereClientIsMemberOfParticipatingTeam(string $firmId, string $clientId,
            string $teamMembershipId, string $teamProgramParticipationId, string $missionPosition): Mission
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            "teamMembershipId" => $teamMembershipId,
            'teamProgramParticipationId' => $teamProgramParticipationId,
            'missionPosition' => $missionPosition,
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

        $programQb = $this->getEntityManager()->createQueryBuilder();
        $programQb->select("t_program.id")
                ->from(TeamProgramParticipation::class, "teamProgramParticipation")
                ->andWhere($programQb->expr()->eq("teamProgramParticipation.id", ":teamProgramParticipationId"))
                ->leftJoin("teamProgramParticipation.team", "team")
                ->andWhere($programQb->expr()->in("team.id", $teamQb->getDQL()))
                ->leftJoin("teamProgramParticipation.programParticipation", "programParticipation")
                ->leftJoin("programParticipation.program", "t_program")
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('mission');
        $qb->select("mission")
                ->andWhere($qb->expr()->eq("mission.position", ":missionPosition"))
                ->leftJoin("mission.program", "program")
                ->andWhere($qb->expr()->in("program.id", $programQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: mission not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aMissionInProgramWhereClientIsMemberOfParticipatingTeam(string $firmId, string $clientId,
            string $teamMembershipId, string $teamProgramParticipationId, string $missionId): Mission
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            "teamMembershipId" => $teamMembershipId,
            'teamProgramParticipationId' => $teamProgramParticipationId,
            'missionId' => $missionId,
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

        $programQb = $this->getEntityManager()->createQueryBuilder();
        $programQb->select("t_program.id")
                ->from(TeamProgramParticipation::class, "teamProgramParticipation")
                ->andWhere($programQb->expr()->eq("teamProgramParticipation.id", ":teamProgramParticipationId"))
                ->leftJoin("teamProgramParticipation.team", "team")
                ->andWhere($programQb->expr()->in("team.id", $teamQb->getDQL()))
                ->leftJoin("teamProgramParticipation.programParticipation", "programParticipation")
                ->leftJoin("programParticipation.program", "t_program")
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('mission');
        $qb->select("mission")
                ->andWhere($qb->expr()->eq("mission.id", ":missionId"))
                ->leftJoin("mission.program", "program")
                ->andWhere($qb->expr()->in("program.id", $programQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: mission not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allMissionsInProgramWhereClientIsMemberOfParticipatingTeam(string $firmId, string $clientId,
            string $teamMembershipId, string $teamProgramParticipationId)
    {
        $statement = <<<_STATEMENT
SELECT y.id, y.name, y.description, y.position, z.submittedWorksheet
FROM (
    SELECT T_Member.Team_id teamId
    FROM T_Member
        LEFT JOIN Client ON Client.id = T_Member.Client_id
    WHERE T_Member.id = :teamMembershipId
        AND Client.id = :clientId
        AND Client.Firm_id = :firmId
)w
LEFT JOIN (
    SELECT TeamParticipant.Team_id teamId, Participant.id participantId, Participant.Program_id programId
    FROM TeamParticipant
        LEFT JOIN Participant ON Participant.id = TeamParticipant.Participant_id
    WHERE Participant.active = true
        AND TeamParticipant.id = :teamProgramParticipationId
)x ON x.teamId = w.teamId
LEFT JOIN (
    SELECT Mission.id, Mission.name, Mission.description, Mission.position, Mission.Program_id programId
    FROM Mission
    WHERE Mission.published = true
    GROUP BY id
)y ON y.programId = x.programId
LEFT JOIN (
    SELECT 
        Worksheet.Participant_id participantId,
        Worksheet.Mission_id missionId,
        COUNT(Worksheet.id) AS submittedWorksheet
    FROM Worksheet
    WHERE Worksheet.removed = false
    GROUP BY missionId, participantId
)z ON z.missionId = y.id AND z.participantId = x.participantId                
_STATEMENT;
        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        $params = [
            "firmId" => $firmId,
            "clientId" => $clientId,
            "teamMembershipId" => $teamMembershipId,
            "teamProgramParticipationId" => $teamProgramParticipationId,
        ];
        $query->execute($params);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function aMissionByPositionBelongsToProgram(string $programId, string $position): Mission
    {
        $params = [
            "programId" => $programId,
            "position" => $position,
        ];

        $qb = $this->createQueryBuilder("mission");
        $qb->select("mission")
                ->andWhere($qb->expr()->eq("mission.position", ":position"))
                ->leftJoin("mission.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: mission not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aPublishedMission(string $id): Mission
    {
        $mission = $this->findOneBy([
            'id' => $id,
            'published' => true,
        ]);
        if (empty($mission)) {
            throw RegularException::notFound('not found: mission not found');
        }
        return $mission;
    }

    public function allPublishedMissionInProgram(string $programId)
    {
        $params = [
            "programId" => $programId,
        ];

        $qb = $this->createQueryBuilder("mission");
        $qb->select("mission")
                ->andWhere($qb->expr()->eq("mission.published", "true"))
                ->leftJoin("mission.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->setParameters($params);

        return $qb->getQuery()->getResult();
    }

    public function allMissionsWithDiscussionOverviewAccessibleByPersonnelHavingMentorAuthority(
            string $personnelId, int $page, int $pageSize)
    {
        $offset = $pageSize * ($page - 1);
        $params = ['personnelId' => $personnelId];
        
        $sql = <<<_STATEMENT
SELECT 
    Mission.id, 
    Mission.name, 
    _a.programId, 
    _a.programName, 
    _a.programConsultationId, 
    _b.lastActivity, 
    _b.numberOfPost, 
    _b.message
FROM Mission
INNER JOIN (
    SELECT Program.id programId, Program.name programName, Consultant.id programConsultationId
    FROM Consultant
    INNER JOIN Program ON Program.id = Consultant.Program_id
    WHERE Consultant.Personnel_id = :personnelId
        AND Consultant.active = true
)_a ON _a.programId = Mission.Program_id
LEFT JOIN (
    SELECT _b2.Mission_id, _b2.modifiedTime lastActivity, _b1.numberOfPost, _b2.message
    FROM (
        SELECT MAX(modifiedTime) modifiedTime, Mission_id, COUNT(id) numberOfPost
        FROM MissionComment
        GROUP BY Mission_id
    )_b1
    INNER JOIN MissionComment _b2 USING (Mission_id, modifiedTime)
)_b ON _b.Mission_id = Mission.id
ORDER BY lastActivity DESC, numberOfPost DESC
LIMIT {$offset}, {$pageSize}
_STATEMENT;
        $statement = $this->getEntityManager()->getConnection()->prepare($sql);
        $resultSet = $statement->executeQuery($params);
        return [
            'total' => $this->totalOfAllMissionsWithDiscussionOverviewAccessibleByPersonnelHavingMentorAuthority($personnelId),
            'list' => $resultSet->fetchAllAssociative(),
        ];
    }
    protected function totalOfAllMissionsWithDiscussionOverviewAccessibleByPersonnelHavingMentorAuthority(string $personnelId)
    {
        $params = ['personnelId' => $personnelId];
        
        $sql = <<<_STATEMENT
SELECT COUNT(Mission.id) total
FROM Mission
INNER JOIN Consultant ON Consultant.Program_id = Mission.Program_id AND Consultant.active = true
WHERE Consultant.Personnel_id = :personnelId
_STATEMENT;
        $statement = $this->getEntityManager()->getConnection()->prepare($sql);
        $resultSet = $statement->executeQuery($params);
        return $resultSet->fetchFirstColumn()[0];
    }

}
