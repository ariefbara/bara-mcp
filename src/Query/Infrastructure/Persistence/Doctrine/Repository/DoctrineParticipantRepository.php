<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Application\Service\Firm\Program\ParticipantRepository;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Model\Firm\Team\Member;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Model\User\UserParticipant;
use Query\Domain\Service\Firm\Program\ParticipantRepository as InterfaceForDomainService;
use Query\Domain\Task\Dependency\Firm\Program\ParticipantFilter;
use Query\Domain\Task\Dependency\Firm\Program\ParticipantRepository as ParticipantRepository2;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineParticipantRepository extends EntityRepository implements ParticipantRepository, InterfaceForDomainService,
        ParticipantRepository2
{

    public function all(
            string $firmId, string $programId, int $page, int $pageSize, ?bool $activeStatus, ?string $note,
            ?string $searchByName)
    {
        $params = [
            "firmId" => $firmId,
            "programId" => $programId,
        ];

        $qb = $this->createQueryBuilder('participant');
        $qb->select('participant')
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ":programId"))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ":firmId"))
                ->setParameters($params);

        if (isset($searchByName)) {
            $clientParticipantQb = $this->getEntityManager()->createQueryBuilder();
            $clientParticipantQb->select('a_participant.id')
                    ->from(ClientParticipant::class, 'clientParticipant')
                    ->leftJoin('clientParticipant.client', 'client')
                    ->orWhere($clientParticipantQb->expr()->like('client.personName.firstName', ":name"))
                    ->orWhere($clientParticipantQb->expr()->like('client.personName.lastName', ":name"))
                    ->leftJoin('clientParticipant.participant', 'a_participant');
            $userParticipantQb = $this->getEntityManager()->createQueryBuilder();

            $userParticipantQb = $this->getEntityManager()->createQueryBuilder();
            $userParticipantQb->select('b_participant.id')
                    ->from(UserParticipant::class, 'userParticipant')
                    ->leftJoin('userParticipant.user', 'user')
                    ->orWhere($userParticipantQb->expr()->like('user.personName.firstName', ":name"))
                    ->orWhere($userParticipantQb->expr()->like('user.personName.lastName', ":name"))
                    ->leftJoin('userParticipant.participant', 'b_participant');

            $teamParticipantQb = $this->getEntityManager()->createQueryBuilder();
            $teamParticipantQb->select('c_participant.id')
                    ->from(TeamProgramParticipation::class, 'teamParticipant')
                    ->leftJoin('teamParticipant.team', 'team')
                    ->orWhere($teamParticipantQb->expr()->like('team.name', ":name"))
                    ->leftJoin('teamParticipant.programParticipation', 'c_participant');

            $qb->andWhere($qb->expr()->orX(
                                    $qb->expr()->in('participant.id', $clientParticipantQb->getDQL()),
                                    $qb->expr()->in('participant.id', $userParticipantQb->getDQL()),
                                    $qb->expr()->in('participant.id', $teamParticipantQb->getDQL())
                            ))
                    ->setParameter('name', "%$searchByName%");
        }


        if (isset($activeStatus)) {
            $qb->andWhere($qb->expr()->eq("participant.active", ":activeStatus"))
                    ->setParameter("activeStatus", $activeStatus);
        }
        if (isset($note)) {
            $qb->andWhere($qb->expr()->eq("participant.note", ":note"))
                    ->setParameter("note", $note);
        }

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofId(string $firmId, string $programId, string $participantId): Participant
    {
        $params = [
            "firmId" => $firmId,
            "programId" => $programId,
            "participantId" => $participantId,
        ];

        $qb = $this->createQueryBuilder('participant');
        $qb->select('participant')
                ->andWhere($qb->expr()->eq('participant.id', ":participantId"))
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ":programId"))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: participant not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function containRecordOfActiveParticipantCorrespondWithClient(string $firmId, string $programId,
            string $clientId): bool
    {
        $params = [
            'clientId' => $clientId,
            'programId' => $programId,
            'firmId' => $firmId,
        ];

        $clientParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $clientParticipantQb->select('cp_participant.id')
                ->from(ClientParticipant::class, 'clientParticipant')
                ->leftJoin('clientParticipant.participant', 'cp_participant')
                ->leftJoin('clientParticipant.client', 'client')
                ->andWhere($clientParticipantQb->expr()->eq('client.id', ':clientId'))
                ->leftJoin('client.firm', 'cp_firm')
                ->andWhere($clientParticipantQb->expr()->eq('cp_firm.id', ':firmId'))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('participant');
        $qb->select('1')
                ->andWhere($qb->expr()->eq('participant.active', 'true'))
                ->andWhere($qb->expr()->in('participant.id', $clientParticipantQb->getDQL()))
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        return !empty($qb->getQuery()->getResult());
    }

    public function containRecordOfActiveParticipantCorrespondWithUser(string $firmId, string $programId, string $userId): bool
    {
        $params = [
            'firmId' => $firmId,
            'programId' => $programId,
            'userId' => $userId,
        ];

        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select('cp_participant.id')
                ->from(UserParticipant::class, 'userParticipant')
                ->leftJoin('userParticipant.participant', 'cp_participant')
                ->leftJoin('userParticipant.user', 'user')
                ->andWhere($participantQb->expr()->eq('user.id', ':userId'))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('participant');
        $qb->select('1')
                ->andWhere($qb->expr()->eq('participant.active', 'true'))
                ->andWhere($qb->expr()->in('participant.id', $participantQb->getDQL()))
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        return !empty($qb->getQuery()->getResult());
    }

    public function aParticipantOfProgram(string $programId, string $participantId): Participant
    {
        $params = [
            "programId" => $programId,
            "participantId" => $participantId,
        ];

        $qb = $this->createQueryBuilder("participant");
        $qb->select("participant")
                ->andWhere($qb->expr()->eq("participant.id", ":participantId"))
                ->leftJoin("participant.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: participant not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allParticipantsOfProgram(string $programId, int $page, int $pageSize, ?bool $activeStatus)
    {
        $params = [
            "programId" => $programId,
        ];

        $qb = $this->createQueryBuilder("participant");
        $qb->select("participant")
                ->leftJoin("participant.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->setParameters($params);

        if (isset($activeStatus)) {
            $qb->andWhere($qb->expr()->eq("participant.active", ":activeStatus"))
                    ->setParameter("activeStatus", $activeStatus);
        }

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function containRecordOfActiveParticipantCorrespondWithTeam(string $teamId, string $programId): bool
    {
        $params = [
            'teamId' => $teamId,
            'programId' => $programId,
        ];

        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select('tp_participant.id')
                ->from(TeamProgramParticipation::class, 'teamParticipant')
                ->leftJoin('teamParticipant.programParticipation', 'tp_participant')
                ->leftJoin('teamParticipant.team', 'team')
                ->andWhere($participantQb->expr()->eq('team.id', ':teamId'))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('participant');
        $qb->select('1')
                ->andWhere($qb->expr()->eq('participant.active', 'true'))
                ->andWhere($qb->expr()->in('participant.id', $participantQb->getDQL()))
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameters($params)
                ->setMaxResults(1);

        return !empty($qb->getQuery()->getResult());
    }

    public function containRecordOfParticipantInFirmCorrespondWithUser(string $firmId, string $userId): bool
    {
        $params = [
            'firmId' => $firmId,
            'userId' => $userId,
        ];

        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select('cp_participant.id')
                ->from(UserParticipant::class, 'userParticipant')
                ->leftJoin('userParticipant.participant', 'cp_participant')
                ->leftJoin('userParticipant.user', 'user')
                ->andWhere($participantQb->expr()->eq('user.id', ':userId'))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('participant');
        $qb->select('1')
                ->andWhere($qb->expr()->eq('participant.active', 'true'))
                ->andWhere($qb->expr()->in('participant.id', $participantQb->getDQL()))
                ->leftJoin('participant.program', 'program')
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        return !empty($qb->getQuery()->getResult());
    }

    public function allActiveIndividualAndTeamProgramParticipationBelongsToClient(string $clientId)
    {
        $params = [
            'clientId' => $clientId,
        ];

        $clientParticipantQB = $this->getEntityManager()->createQueryBuilder();
        $clientParticipantQB->select('a_participant.id')
                ->from(ClientParticipant::class, 'clientParticipant')
                ->leftJoin('clientParticipant.participant', 'a_participant')
                ->leftJoin('clientParticipant.client', 'a_client')
                ->andWhere($clientParticipantQB->expr()->eq('a_client.id', ':clientId'));

        $activeTeamMemberQB = $this->getEntityManager()->createQueryBuilder();
        $activeTeamMemberQB->select('b1_team.id')
                ->from(Member::class, 'b1_member')
                ->andWhere($activeTeamMemberQB->expr()->eq('b1_member.active', 'true'))
                ->leftJoin('b1_member.team', 'b1_team')
                ->leftJoin('b1_member.client', 'b1_client')
                ->andWhere($activeTeamMemberQB->expr()->eq('b1_client.id', ':clientId'));

        $teamParticipantQB = $this->getEntityManager()->createQueryBuilder();
        $teamParticipantQB->select('b_participant.id')
                ->from(TeamProgramParticipation::class, 'teamParticipant')
                ->leftJoin('teamParticipant.programParticipation', 'b_participant')
                ->leftJoin('teamParticipant.team', 'b_team')
                ->andWhere($teamParticipantQB->expr()->in('b_team.id', $activeTeamMemberQB->getDQL()));

        $qb = $this->createQueryBuilder('participant');
        $qb->select('participant')
                ->andWhere($qb->expr()->eq('participant.active', 'true'))
                ->andWhere($qb->expr()->orX(
                                $qb->expr()->in('participant.id', $clientParticipantQB->getDQL()),
                                $qb->expr()->in('participant.id', $teamParticipantQB->getDQL()),
                        ))
                ->setParameters($params);
        return $qb->getQuery()->getResult();
    }

    public function aProgramParticipantInFirm(string $firmId, string $id): Participant
    {
        $parameters = [
            'firmId' => $firmId,
            'id' => $id,
        ];

        $qb = $this->createQueryBuilder('participant');
        $qb->select('participant')
                ->andWhere($qb->expr()->eq('participant.id', ':id'))
                ->leftJoin('participant.program', 'program')
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($parameters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: program participant not found');
        }
    }

    public function allProgramParticipantsInFirm(string $firmId, ParticipantFilter $filter)
    {
        $parameters = [
            'firmId' => $firmId,
        ];

        $qb = $this->createQueryBuilder('participant');
        $qb->select('participant')
                ->leftJoin('participant.program', 'program')
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($parameters);

        if (!empty($filter->getProgramIdList())) {
            $qb->andWhere($qb->expr()->in('program.id', ':programIdList'))
                    ->setParameter('programIdList', $filter->getProgramIdList());
        }

        $activeStatus = $filter->getActiveStatus();
        if (isset($activeStatus)) {
            $qb->andWhere($qb->expr()->eq('participant.active', ':activeStatus'))
                    ->setParameter('activeStatus', $activeStatus);
        }

        return PaginatorBuilder::build($qb->getQuery(), $filter->getPage(), $filter->getPageSize());
    }

    public function summaryOfAllParticipantsWithDedicatedMentorCorrespondToPersonnel(
            string $personnelId, int $page, int $pageSize, string $orderType = "DESC")
    {
        $offset = $pageSize * ($page - 1);
        $statement = <<<_STATEMENT
SELECT 
    _a.participantId,
    COALESCE(_b.userName, _c.clientName, _d.teamName) participantName,
    _a.totalCompletedMission,
    _e.totalMission,
    ROUND (_f.achievement * 100) metricAchievement,
    _f.completedMetric,
    _f.totalAssignedMetric
FROM (
    SELECT 
        Participant.id participantId, 
        Participant.Program_id programId,
        _a1.totalCompletedMission
    FROM DedicatedMentor
    LEFT JOIN Consultant ON DedicatedMentor.Consultant_id = Consultant.id
    LEFT JOIN Participant ON DedicatedMentor.Participant_id = Participant.id
    LEFT JOIN (
        SELECT Participant_id, COUNT(DISTINCT Mission_id) totalCompletedMission
        FROM CompletedMission
        GROUP BY Participant_id
    )_a1 ON _a1.Participant_id = Participant.id
    WHERE Consultant.Personnel_id = :personnelId
        AND Consultant.active = true
        AND Participant.active = true
        AND DedicatedMentor.cancelled = false
)_a
LEFT JOIN (
    SELECT CONCAT(User.firstName, ' ', COALESCE(User.lastName, '')) userName, UserParticipant.Participant_id participantId
    FROM UserParticipant
        LEFT JOIN User ON User.id = UserParticipant.User_id
)_b ON _b.participantId = _a.participantId
LEFT JOIN (
    SELECT CONCAT(Client.firstName, ' ', COALESCE(Client.lastName, '')) clientName, ClientParticipant.Participant_id participantId
    FROM ClientParticipant
        LEFT JOIN Client ON Client.id = ClientParticipant.Client_id
)_c ON _c.participantId = _a.participantId
LEFT JOIN (
    SELECT Team.name teamName, TeamParticipant.Participant_id participantId
    FROM TeamParticipant
        LEFT JOIN Team ON Team.id = TeamParticipant.Team_id
)_d ON _d.participantId = _a.participantId
LEFT JOIN (
    SELECT COUNT(*) totalMission, Mission.Program_id programId
    FROM Mission
    WHERE Mission.Published = true
    GROUP BY programId
)_e ON _e.programId = _a.programId
LEFT JOIN (
    SELECT 
        MetricAssignment.Participant_id,
        CASE WHEN COUNT(_f3.target) IS NULL THEN NULL
            ELSE SUM(__d.inputValue/_f3.target)/COUNT(_f3.target)
        END achievement,
        CASE WHEN COUNT(_f3.target) IS NULL THEN NULL
            ELSE SUM(CASE WHEN __d.inputValue >= _f3.target THEN 1 ELSE 0 END)
        END completedMetric,
        COUNT(_f3.target) totalAssignedMetric,
        _f2.id reportId
    FROM (
        SElECT MetricAssignment_id, MAX(observationTime) observationTime
        FROM MetricAssignmentReport
        WHERE approved = true AND removed = false
        GROUP BY MetricAssignment_id
    )_f1 
    INNER JOIN MetricAssignmentReport _f2 USING (MetricAssignment_id, observationTime)
    LEFT JOIN MetricAssignment ON MetricAssignment.id = _f2.MetricAssignment_id
    LEFT JOIN (
        SELECT id, `target`, MetricAssignment_id
        FROM AssignmentField
        WHERE disabled = false
    )_f3 ON _f3.MetricAssignment_id = _f2.MetricAssignment_id
    LEFT JOIN (
        SELECT inputValue, MetricAssignmentReport_id, AssignmentField_id
        FROM AssignmentFieldValue
        WHERE removed = false
    )__d ON __d.MetricAssignmentReport_id = _f2.id AND __d.AssignmentField_id = _f3.id
    WHERE _f2.approved = true AND _f2.removed = false
    GROUP BY reportId
)_f ON _f.Participant_id = _a.participantId
ORDER BY totalCompletedMission {$orderType}, metricAchievement {$orderType}
LIMIT {$offset}, {$pageSize}
_STATEMENT;
        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        $params = ["personnelId" => $personnelId];
        $query->execute($params);

        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function countOfAllParticipantsWithDedicatedMentorCorrespondToPersonnel(string $personnelId)
    {
        $statement = <<<_STATEMENT
SELECT Consultant.Personnel_id personnelId, COUNT(DedicatedMentor.id) total
FROM DedicatedMentor
LEFT JOIN Consultant ON Consultant.id = DedicatedMentor.Consultant_id
LEFT JOIN Participant ON Participant.id = DedicatedMentor.Participant_id
WHERE Consultant.Personnel_id = :personnelId
    AND Consultant.active = true
    AND Participant.active = true
    AND DedicatedMentor.cancelled = false
GROUP BY personnelId
_STATEMENT;
        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        $params = ["personnelId" => $personnelId];
        $query->execute($params);

        $result = $query->fetchAll(\PDO::FETCH_ASSOC);
        return $result[0]["total"];
    }

}
