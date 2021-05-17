<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use PDO;
use Query\Application\Service\Client\ConsultationRequestRepository as InterfaceForClient;
use Query\Application\Service\Firm\Program\ConsultationRequestRepository;
use Query\Application\Service\Personnel\ConsultationRequestRepository as InterfaceForPersonnel;
use Query\Application\Service\User\ConsultationRequestRepository as InterfaceForUser;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest;
use Query\Domain\Model\Firm\Team\Member;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Model\User\UserParticipant;
use Query\Infrastructure\QueryFilter\ConsultationRequestFilter;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineConsultationRequestRepository extends EntityRepository implements ConsultationRequestRepository, InterfaceForPersonnel,
        InterfaceForClient, InterfaceForUser
{

    public function aConsultationRequestOfClient(
            string $firmId, string $clientId, string $programParticipationId, string $consultationRequestId): ConsultationRequest
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'programParticipationId' => $programParticipationId,
            'consultationRequestId' => $consultationRequestId,
        ];

        $clientParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $clientParticipantQb->select('t_participant.id')
                ->from(ClientParticipant::class, 'clientParticipant')
                ->andWhere($clientParticipantQb->expr()->eq('clientParticipant.id', ':programParticipationId'))
                ->leftJoin('clientParticipant.client', 'client')
                ->leftJoin('clientParticipant.participant', 't_participant')
                ->andWhere($clientParticipantQb->expr()->eq('client.id', ':clientId'))
                ->leftJoin('client.firm', 'firm')
                ->andWhere($clientParticipantQb->expr()->eq('firm.id', ':firmId'))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('consultationRequest');
        $qb->select('consultationRequest')
                ->andWhere($qb->expr()->eq('consultationRequest.id', ':consultationRequestId'))
                ->leftJoin('consultationRequest.participant', 'participant')
                ->andWhere($qb->expr()->in('participant.id', $clientParticipantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: consultation request not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allConsultationRequestsOfClient(
            string $firmId, string $clientId, string $programParticipationId, int $page, int $pageSize,
            ?ConsultationRequestFilter $consultationRequestFilter)
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'programParticipationId' => $programParticipationId,
        ];

        $clientParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $clientParticipantQb->select('t_participant.id')
                ->from(ClientParticipant::class, 'clientParticipant')
                ->andWhere($clientParticipantQb->expr()->eq('clientParticipant.id', ':programParticipationId'))
                ->leftJoin('clientParticipant.client', 'client')
                ->leftJoin('clientParticipant.participant', 't_participant')
                ->andWhere($clientParticipantQb->expr()->eq('client.id', ':clientId'))
                ->leftJoin('client.firm', 'firm')
                ->andWhere($clientParticipantQb->expr()->eq('firm.id', ':firmId'))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('consultationRequest');
        $qb->select('consultationRequest')
                ->leftJoin('consultationRequest.participant', 'participant')
                ->andWhere($qb->expr()->in('participant.id', $clientParticipantQb->getDQL()))
                ->setParameters($params);

        $this->applyFilter($qb, $consultationRequestFilter);
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function aConsultationRequestFromUserParticipant(string $userId, string $userParticipantId,
            string $consultationRequestId): ConsultationRequest
    {
        $params = [
            'userId' => $userId,
            'userParticipantId' => $userParticipantId,
            'consultationRequestId' => $consultationRequestId,
        ];

        $userParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $userParticipantQb->select('t_participant.id')
                ->from(UserParticipant::class, 'userParticipant')
                ->andWhere($userParticipantQb->expr()->eq('userParticipant.id', ':userParticipantId'))
                ->leftJoin('userParticipant.participant', 't_participant')
                ->leftJoin('userParticipant.user', 'user')
                ->andWhere($userParticipantQb->expr()->eq('user.id', ':userId'))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('consultationRequest');
        $qb->select('consultationRequest')
                ->andWhere($qb->expr()->eq('consultationRequest.id', ':consultationRequestId'))
                ->leftJoin('consultationRequest.participant', 'participant')
                ->andWhere($qb->expr()->in('participant.id', $userParticipantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: consultation request not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allConsultationRequestFromUserParticipant(string $userId, string $userParticipantId, int $page,
            int $pageSize, ?ConsultationRequestFilter $consultationRequestFilter)
    {
        $params = [
            'userId' => $userId,
            'userParticipantId' => $userParticipantId,
        ];

        $userParticipantQb = $this->getEntityManager()->createQueryBuilder();
        $userParticipantQb->select('t_participant.id')
                ->from(UserParticipant::class, 'userParticipant')
                ->andWhere($userParticipantQb->expr()->eq('userParticipant.id', ':userParticipantId'))
                ->leftJoin('userParticipant.participant', 't_participant')
                ->leftJoin('userParticipant.user', 'user')
                ->andWhere($userParticipantQb->expr()->eq('user.id', ':userId'))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('consultationRequest');
        $qb->select('consultationRequest')
                ->leftJoin('consultationRequest.participant', 'participant')
                ->andWhere($qb->expr()->in('participant.id', $userParticipantQb->getDQL()))
                ->setParameters($params);

        $this->applyFilter($qb, $consultationRequestFilter);
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function aConsultationRequestBelongsToTeam(string $teamId, string $consultationRequestId): ConsultationRequest
    {
        $params = [
            "teamId" => $teamId,
            "consultationRequestId" => $consultationRequestId,
        ];
        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("programParticipation.id")
                ->from(TeamProgramParticipation::class, "teamProgramParticipation")
                ->leftJoin("teamProgramParticipation.programParticipation", "programParticipation")
                ->leftJoin("teamProgramParticipation.team", "team")
                ->andWhere($participantQb->expr()->eq("team.id", ":teamId"))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder("consultationRequest");
        $qb->select("consultationRequest")
                ->andWhere($qb->expr()->eq("consultationRequest.id", ":consultationRequestId"))
                ->leftJoin("consultationRequest.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $participantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: consultation request not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allConsultationRequestsBelongsInProgramParticipationOfTeam(string $teamId,
            string $teamProgramParticipationId, int $page, int $pageSize,
            ?ConsultationRequestFilter $consultationRequestFilter)
    {
        $params = [
            "teamId" => $teamId,
            "teamProgramParticipationId" => $teamProgramParticipationId,
        ];
        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("programParticipation.id")
                ->from(TeamProgramParticipation::class, "teamProgramParticipation")
                ->andWhere($participantQb->expr()->eq("teamProgramParticipation.id", ":teamProgramParticipationId"))
                ->leftJoin("teamProgramParticipation.programParticipation", "programParticipation")
                ->leftJoin("teamProgramParticipation.team", "team")
                ->andWhere($participantQb->expr()->eq("team.id", ":teamId"))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder("consultationRequest");
        $qb->select("consultationRequest")
                ->leftJoin("consultationRequest.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $participantQb->getDQL()))
                ->setParameters($params);

        $this->applyFilter($qb, $consultationRequestFilter);
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function aConsultationRequestBelongsToConsultant(
            string $personnelId, string $programConsultationId, string $consultationRequestId): ConsultationRequest
    {
        $params = [
            "personnelId" => $personnelId,
            "programConsultationId" => $programConsultationId,
            "consultationRequestId" => $consultationRequestId,
        ];

        $qb = $this->createQueryBuilder("consultationRequest");
        $qb->select("consultationRequest")
                ->andWhere($qb->expr()->eq("consultationRequest.id", ":consultationRequestId"))
                ->leftJoin("consultationRequest.consultant", "programConsultation")
                ->andWhere($qb->expr()->eq("programConsultation.id", ":programConsultationId"))
                ->leftJoin("programConsultation.personnel", "personnel")
                ->andWhere($qb->expr()->eq("personnel.id", ":personnelId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: consultation request not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allConsultationRequestBelongsToConsultant(
            string $personnelId, string $programConsultationId, int $page, int $pageSize,
            ?ConsultationRequestFilter $consultationRequestFilter)
    {
        $params = [
            "personnelId" => $personnelId,
            "programConsultationId" => $programConsultationId,
        ];

        $qb = $this->createQueryBuilder("consultationRequest");
        $qb->select("consultationRequest")
                ->leftJoin("consultationRequest.consultant", "programConsultation")
                ->andWhere($qb->expr()->eq("programConsultation.id", ":programConsultationId"))
                ->leftJoin("programConsultation.personnel", "personnel")
                ->andWhere($qb->expr()->eq("personnel.id", ":personnelId"))
                ->setParameters($params);

        $this->applyFilter($qb, $consultationRequestFilter);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function aConsultationRequestInProgram(string $programId, string $consultationRequestId): ConsultationRequest
    {
        $params = [
            "programId" => $programId,
            "consultationRequestId" => $consultationRequestId,
        ];

        $qb = $this->createQueryBuilder("consultationRequest");
        $qb->select("consultationRequest")
                ->andWhere($qb->expr()->eq("consultationRequest.id", ":consultationRequestId"))
                ->leftJoin("consultationRequest.participant", "participant")
                ->leftJoin("participant.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: consultation request not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allConsultationRequestsInProgram(
            string $programId, int $page, int $pageSize, ?ConsultationRequestFilter $consultationRequestFilter)
    {
        $params = [
            "programId" => $programId,
        ];

        $qb = $this->createQueryBuilder("consultationRequest");
        $qb->select("consultationRequest")
                ->leftJoin("consultationRequest.participant", "participant")
                ->leftJoin("participant.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->setParameters($params);

        $this->applyFilter($qb, $consultationRequestFilter);
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function allConsultationRequestBelongsToPersonnel(
            string $personnelId, int $page, int $pageSize, ?ConsultationRequestFilter $consultationRequestFilter)
    {
        $em = $this->getEntityManager();
        $params = [
            "personnelId" => $personnelId,
        ];
        
        $minStartTimeCondition = "";
        $minStartTime = $consultationRequestFilter->getMinStartTime();
        if (isset($minStartTime)) {
            $minStartTimeCondition = "AND ConsultationRequest.startDateTime >= :minStartTime";
            $params['minStartTime'] = $minStartTime->format('Y-m-d H:i:s');
        }
        
        $maxEndTime = $consultationRequestFilter->getMaxEndTime();
        $maxEndTimeCondition = "";
        if (isset($maxEndTime)) {
            $maxEndTimeCondition = "AND ConsultationRequest.endDateTime <= :maxEndTime";
            $params['maxEndTime'] = $maxEndTime->format('Y-m-d H:i:s');
        }
        
        $concludedStatusCondition = "";
        $concludedStatus = $consultationRequestFilter->getConcludedStatus();
        if (isset($concludedStatus)) {
            $concludedStatusCondition = "AND ConsultationRequest.concluded = :concludedStatus";
            $params['concludedStatus'] = $concludedStatus;
        }
        $statusCondition = "";
        $status = $consultationRequestFilter->getStatus();
        if (!empty($status)) {
            $statusCondition = "AND ConsultationRequest.status IN " . implode(', ', $status);
        }
        
        $totalStatement = <<<_TOTAL
SELECT COUNT(ConsultationRequest.id) total
FROM ConsultationRequest
LEFT JOIN Consultant ON Consultant.id = ConsultationRequest.Consultant_id
WHERE Consultant.Personnel_id = :personnelId 
    AND Consultant.active = true
    {$minStartTimeCondition}
    {$maxEndTimeCondition}
    {$concludedStatusCondition}
    {$statusCondition}
_TOTAL;
        $totalQuery = $em->getConnection()->prepare($totalStatement);
        $totalQuery->execute($params);
        $total = $totalQuery->fetchAll(PDO::FETCH_ASSOC)[0]['total'];
        
        $offset = $pageSize * ($page - 1);
        $statement = <<<_STATEMENT
SELECT
    ConsultationRequest.id,
    Consultant.id consultantId,
    Consultant.Program_id programId,
    ConsultationRequest.concluded,
    ConsultationRequest.status,
    ConsultationRequest.startDateTime startTime,
    ConsultationRequest.endDateTime endTime,
    ConsultationRequest.media,
    ConsultationRequest.address,
    COALESCE(_c.userName, _d.clientName, _e.teamName) participantName, 
    IF (_a.id IS NULL, false, true) isDedicatedMentor
FROM ConsultationRequest
LEFT JOIN Consultant ON Consultant.id = ConsultationRequest.Consultant_id
LEFT JOIN (
    SELECT id, Consultant_id, Participant_id
    FROM DedicatedMentor
)_a ON _a.Consultant_id = Consultant.id AND _a.Participant_id = ConsultationRequest.Participant_id
LEFT JOIN (
    SELECT CONCAT(User.firstName, ' ', COALESCE(User.lastName, '')) userName, UserParticipant.Participant_id participantId
    FROM UserParticipant
        LEFT JOIN User ON User.id = UserParticipant.User_id
)_c ON _c.participantId = ConsultationRequest.Participant_id
LEFT JOIN (
    SELECT CONCAT(Client.firstName, ' ', COALESCE(Client.lastName, '')) clientName, ClientParticipant.Participant_id participantId
    FROM ClientParticipant
        LEFT JOIN Client ON Client.id = ClientParticipant.Client_id
)_d ON _d.participantId = ConsultationRequest.Participant_id
LEFT JOIN (
    SELECT Team.name teamName, TeamParticipant.Participant_id participantId
    FROM TeamParticipant
        LEFT JOIN Team ON Team.id = TeamParticipant.Team_id
)_e ON _e.participantId = ConsultationRequest.Participant_id
WHERE Consultant.Personnel_id = :personnelId 
    AND Consultant.active = true
    {$minStartTimeCondition}
    {$maxEndTimeCondition}
    {$concludedStatusCondition}
    {$statusCondition}
ORDER BY isDedicatedMentor DESC
LIMIT {$offset}, {$pageSize}
_STATEMENT;
        
        $offset = $pageSize * ($page - 1);
        $query = $em->getConnection()->prepare($statement);
        $query->execute($params);
        return [
            'total' => $total,
            'list' => $query->fetchAll(PDO::FETCH_ASSOC),
        ];
    }

    protected function applyFilter(QueryBuilder $qb, ?ConsultationRequestFilter $consultationRequestFilter): void
    {
        if (!isset($consultationRequestFilter)) {
            return;
        }

        if (!is_null($consultationRequestFilter->getMinStartTime())) {
            $qb->andWhere($qb->expr()->gte("consultationRequest.startEndTime.startDateTime", ":minStartTime"))
                    ->setParameter("minStartTime", $consultationRequestFilter->getMinStartTime());
        }
        if (!is_null($consultationRequestFilter->getMaxEndTime())) {
            $qb->andWhere($qb->expr()->lte("consultationRequest.startEndTime.endDateTime", ":maxEndTime"))
                    ->setParameter("maxEndTime", $consultationRequestFilter->getMaxEndTime());
        }
        if (!is_null($consultationRequestFilter->getConcludedStatus())) {
            $qb->andWhere($qb->expr()->eq("consultationRequest.concluded", ":concludedStatus"))
                    ->setParameter("concludedStatus", $consultationRequestFilter->getConcludedStatus());
        }
        if (!is_null($consultationRequestFilter->getStatus())) {
            $qb->andWhere($qb->expr()->in("consultationRequest.status", ":status"))
                    ->setParameter("status", $consultationRequestFilter->getStatus());
        }
    }

    public function allAccessibleConsultationSesssionBelongsToClient(string $clientId, int $page, int $pageSize,
            ?ConsultationRequestFilter $consultationRequestFilter)
    {
        $params = [
            'clientId' => $clientId,
        ];

        $clientParticipantQB = $this->getEntityManager()->createQueryBuilder();
        $clientParticipantQB->select('a_participant.id')
                ->from(ClientParticipant::class, 'a_clientParticipant')
                ->leftJoin('a_clientParticipant.participant', 'a_participant')
                ->leftJoin('a_clientParticipant.client', 'a_client')
                ->andWhere($clientParticipantQB->expr()->eq('a_client.id', ':clientId'));

        $teamMemberQB = $this->getEntityManager()->createQueryBuilder();
        $teamMemberQB->select('b_team.id')
                ->from(Member::class, 'b_member')
                ->andWhere($teamMemberQB->expr()->eq('b_member.active', 'true'))
                ->leftJoin('b_member.client', 'b_client')
                ->andWhere($teamMemberQB->expr()->eq('b_client.id', ':clientId'))
                ->leftJoin('b_member.team', 'b_team');

        $teamParticipantQB = $this->getEntityManager()->createQueryBuilder();
        $teamParticipantQB->select('c_participant.id')
                ->from(TeamProgramParticipation::class, 'c_teamParticipant')
                ->leftJoin('c_teamParticipant.programParticipation', 'c_participant')
                ->leftJoin('c_teamParticipant.team', 'c_team')
                ->andWhere($teamParticipantQB->expr()->in('c_team.id', $teamMemberQB->getDQL()));

        $qb = $this->createQueryBuilder('consultationRequest');
        $qb->select('consultationRequest')
                ->leftJoin('consultationRequest.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.active', 'true'))
                ->andWhere($qb->expr()->orX(
                                $qb->expr()->in('participant.id', $clientParticipantQB->getDQL()),
                                $qb->expr()->in('participant.id', $teamParticipantQB->getDQL())
                ))
                ->addOrderBy('consultationRequest.startEndTime.startDateTime', 'ASC')
                ->setParameters($params);

        $this->applyFilter($qb, $consultationRequestFilter);
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function allConsultationRequestBelongsToUser(
            string $userId, int $page, int $pageSize, ?ConsultationRequestFilter $consultationRequestFilter)
    {
        $params = [
            'userId' => $userId,
        ];

        $userParticipantQB = $this->getEntityManager()->createQueryBuilder();
        $userParticipantQB->select('a_participant.id')
                ->from(UserParticipant::class, 'a_userParticipant')
                ->leftJoin('a_userParticipant.participant', 'a_participant')
                ->leftJoin('a_userParticipant.user', 'a_user')
                ->andWhere($userParticipantQB->expr()->eq('a_user.id', ':userId'));

        $qb = $this->createQueryBuilder('consultationRequest');
        $qb->select('consultationRequest')
                ->leftJoin('consultationRequest.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.active', 'true'))
                ->andWhere($qb->expr()->in('participant.id', $userParticipantQB->getDQL()))
                ->addOrderBy('consultationRequest.startEndTime.startDateTime', 'ASC')
                ->setParameters($params);

        $this->applyFilter($qb, $consultationRequestFilter);
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
