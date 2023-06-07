<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Query\Domain\Model\Firm\Program\Participant\MentoringRequest;
use Query\Domain\Task\Dependency\Firm\Program\Participant\MentoringRequestRepository;
use Query\Domain\Task\Dependency\Firm\Program\Participant\MentoringRequestSearch;
use Query\Domain\Task\Dependency\PaginationFilter;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;
use SharedContext\Domain\ValueObject\MentoringRequestStatus;

class DoctrineMentoringRequestRepository extends DoctrineEntityRepository implements MentoringRequestRepository
{

    public function aMentoringRequestBelongsToParticipant(string $participantId, string $id): MentoringRequest
    {
        $paramenters = [
            'participantId' => $participantId,
            'id' => $id,
        ];

        $qb = $this->createQueryBuilder('mentoringRequest');
        $qb->select('mentoringRequest')
                ->andWhere($qb->expr()->eq('mentoringRequest.id', ':id'))
                ->leftJoin('mentoringRequest.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->setParameters($paramenters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: mentoring request not found');
        }
    }

    public function aMentoringRequestBelongsToPersonnel(string $personnelId, string $id): MentoringRequest
    {
        $paramenters = [
            'personnelId' => $personnelId,
            'id' => $id,
        ];

        $qb = $this->createQueryBuilder('mentoringRequest');
        $qb->select('mentoringRequest')
                ->andWhere($qb->expr()->eq('mentoringRequest.id', ':id'))
                ->leftJoin('mentoringRequest.mentor', 'mentor')
                ->leftJoin('mentor.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->setParameters($paramenters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: mentoring request not found');
        }
    }

    public function allMentoringRequestBelongsToPersonnel(
            string $personnelId, int $page, int $pageSize, MentoringRequestSearch $mentoringRequestSearch)
    {
        $paramenters = [
            'personnelId' => $personnelId,
        ];

        $qb = $this->createQueryBuilder('mentoringRequest');
        $qb->select('mentoringRequest')
                ->leftJoin('mentoringRequest.mentor', 'mentor')
                ->andWhere($qb->expr()->eq('mentor.active', 'true'))
                ->leftJoin('mentor.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->addOrderBy('mentoringRequest.schedule.startTime', $mentoringRequestSearch->getOrderDirection())
                ->setParameters($paramenters);

        if ($mentoringRequestSearch->getFrom()) {
            $qb->andWhere($qb->expr()->gte('mentoringRequest.schedule.startTime', ':from'))
                    ->setParameter('from', $mentoringRequestSearch->getFrom());
        }
        if ($mentoringRequestSearch->getTo()) {
            $qb->andWhere($qb->expr()->lte('mentoringRequest.schedule.endTime', ':to'))
                    ->setParameter('to', $mentoringRequestSearch->getTo());
        }
        if (!empty($mentoringRequestSearch->getRequestStatusList())) {
            $qb->andWhere($qb->expr()->in('mentoringRequest.requestStatus.value', ':requestStatusList'))
                    ->setParameter('requestStatusList', $mentoringRequestSearch->getRequestStatusList());
        }

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function aMentoringRequestInProgram(string $programId, string $id): MentoringRequest
    {
        $paramenters = [
            'programId' => $programId,
            'id' => $id,
        ];

        $qb = $this->createQueryBuilder('mentoringRequest');
        $qb->select('mentoringRequest')
                ->andWhere($qb->expr()->eq('mentoringRequest.id', ':id'))
                ->leftJoin('mentoringRequest.participant', 'participant')
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameters($paramenters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: mentoring request not found');
        }
    }

    public function allUnconcludedMentoringRequestsInProgramManageableByPersonnel(
            string $personnelId, PaginationFilter $paginationFilter)
    {
        $offset = $paginationFilter->getPageSize() * ($paginationFilter->getPage() - 1);
        $offeredByMentorStatus = MentoringRequestStatus::OFFERED;
        $requestedByParticipantStatus = MentoringRequestStatus::REQUESTED;

        $parameters = [
            "personnelId" => $personnelId,
        ];
        $statement = <<<_STATEMENT
SELECT 
    MentoringRequest.id, 
    CASE MentoringRequest.requestStatus
        WHEN 1 THEN 'offered by mentor'
        ELSE 'requested by participant'
    END requestStatus,
    MentoringRequest.startTime, 
    MentoringRequest.endTime, 
    MentoringRequest.mediaType, 
    MentoringRequest.location,
    MentoringRequest.Participant_id participantId, 
    COALESCE(_b.userName, _c.clientName, _d.teamName) participantName,
    MentoringRequest.Consultant_id consultantId, 
    CONCAT(Personnel.firstName, ' ', COALESCE(Personnel.lastName, '')) consultantName, 
    _a.programId,
    _a.programName
FROM MentoringRequest
INNER JOIN Participant ON Participant.id = MentoringRequest.Participant_id
INNER JOIN Consultant ON Consultant.id = MentoringRequest.Consultant_id
INNER JOIN Personnel ON Personnel.id = Consultant.Personnel_id
INNER JOIN (
    SELECT Coordinator.id coordinatorId, Program.id programId, Program.name programName
    FROM Coordinator
    INNER JOIN Program ON Program.id = Coordinator.Program_id
    WHERE Coordinator.active = true
        AND Coordinator.Personnel_id = :personnelId
)_a ON _a.programId = Participant.Program_id
LEFT JOIN (
    SELECT CONCAT(User.firstName, ' ', COALESCE(User.lastName, '')) userName, UserParticipant.Participant_id participantId
    FROM UserParticipant
        LEFT JOIN User ON User.id = UserParticipant.User_id
)_b ON _b.participantId = Participant.id
LEFT JOIN (
    SELECT CONCAT(Client.firstName, ' ', COALESCE(Client.lastName, '')) clientName, ClientParticipant.Participant_id participantId
    FROM ClientParticipant
        LEFT JOIN Client ON Client.id = ClientParticipant.Client_id
)_c ON _c.participantId = Participant.id
LEFT JOIN (
    SELECT Team.name teamName, TeamParticipant.Participant_id participantId
    FROM TeamParticipant
        LEFT JOIN Team ON Team.id = TeamParticipant.Team_id
)_d ON _d.participantId = Participant.id
WHERE Participant.active = true
    AND MentoringRequest.requestStatus IN ({$requestedByParticipantStatus}, {$offeredByMentorStatus})
ORDER BY MentoringRequest.startTime ASC
LIMIT {$offset}, {$paginationFilter->getPageSize()}
_STATEMENT;

        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        return [
            'total' => $this->countOfAllUnconcludedMentoringRequestsInProgramManageableByPersonnel($personnelId, $paginationFilter),
            'list' => $query->executeQuery($parameters)->fetchAllAssociative(),
        ];
    }
    public function countOfAllUnconcludedMentoringRequestsInProgramManageableByPersonnel(
            string $personnelId, PaginationFilter $paginationFilter)
    {
        $offeredByMentorStatus = MentoringRequestStatus::OFFERED;
        $requestedByParticipantStatus = MentoringRequestStatus::REQUESTED;

        $parameters = [
            "personnelId" => $personnelId,
        ];
        $statement = <<<_STATEMENT
SELECT COUNT(*) total
FROM MentoringRequest
INNER JOIN Participant ON Participant.id = MentoringRequest.Participant_id
INNER JOIN (
    SELECT Coordinator.Program_id programId
    FROM Coordinator
    WHERE Coordinator.active = true
        AND Coordinator.Personnel_id = :personnelId
)_a ON _a.programId = Participant.Program_id
WHERE Participant.active = true
    AND MentoringRequest.requestStatus IN ({$requestedByParticipantStatus}, {$offeredByMentorStatus})
_STATEMENT;

        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        return $query->executeQuery($parameters)->fetchFirstColumn()[0];
    }

}
