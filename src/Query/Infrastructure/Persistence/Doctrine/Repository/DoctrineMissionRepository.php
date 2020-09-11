<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use PDO;
use Query\ {
    Application\Service\Firm\Program\MissionRepository,
    Domain\Model\Firm\Client\ClientParticipant,
    Domain\Model\Firm\Program\Mission,
    Domain\Model\User\UserParticipant
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineMissionRepository extends EntityRepository implements MissionRepository
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

    public function all(string $firmId, string $programId, int $page, int $pageSize, ?string $position)
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

        if (isset($position)) {
            $qb->andWhere($qb->expr()->eq("mission.position", ":position"))
                    ->setParameter("position", $position);
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

}
