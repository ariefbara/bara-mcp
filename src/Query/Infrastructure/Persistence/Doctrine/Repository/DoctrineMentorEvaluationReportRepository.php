<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use PDO;
use Query\Domain\Model\Firm\Program;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportFilter;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportRepository;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportSummaryFilter;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportTranscriptFilter;
use Resources\Exception\RegularException;

class DoctrineMentorEvaluationReportRepository extends EntityRepository implements EvaluationReportRepository
{

    public function allEvaluationReportsBelongsToPersonnel(
            string $personnelId, string $programId, int $page, int $pageSize,
            EvaluationReportFilter $evaluationReportFilter)
    {
        $evaluationPlanFilterClause = '';
        $participantFilterClause = '';
        $submittedStatusFilterClause = '';

        $params = [
            "personnelId" => $personnelId,
            "programId" => $programId,
        ];
        if (!empty($evaluationPlanId = $evaluationReportFilter->getEvaluationPlanId())) {
            $evaluationPlanFilterClause = "AND EvaluationPlan.id = :evaluationPlanId";
            $params['evaluationPlanId'] = $evaluationPlanId;
        }
        if (!empty($participantName = $evaluationReportFilter->getParticipantName())) {
            $participantFilterClause = "HAVING participantName LIKE :participantName";
            $params['participantName'] = "%{$participantName}%";
        }
        if (!is_null($submittedStatus = $evaluationReportFilter->getSubmittedStatus())) {
            $submittedStatusFilterClause = $submittedStatus ?
                    "WHERE (MentorEvaluationReport.id IS NOT NULL AND MentorEvaluationReport.cancelled = false)" :
                    "WHERE MentorEvaluationReport.id IS NULL";
        }

        $offset = $pageSize * ($page - 1);
        $statement = <<<_STATEMENT
SELECT 
    _a.dedicatedMentorId, 
    _a.participantId, 
    _a.participantName, 
    _a.evaluationPlanId, 
    _a.evaluationPlanName, 
    _a.evaluationPlanIntervalDay, 
    MentorEvaluationReport.id mentorEvaluationReportId
FROM (
    SELECT 
        DedicatedMentor.id dedicatedMentorId,
        Participant.id participantId,
        COALESCE(_a1.userName, _a2.clientName, _a3.teamName) participantName, 
        EvaluationPlan.id evaluationPlanId, 
        EvaluationPlan.name evaluationPlanName, 
        EvaluationPlan.days_interval evaluationPlanIntervalDay
    FROM DedicatedMentor
    LEFT JOIN Participant ON Participant.id = DedicatedMentor.Participant_id
    LEFT JOIN (
        SELECT CONCAT(User.firstName, ' ', COALESCE(User.lastName, '')) userName, UserParticipant.Participant_id participantId
        FROM UserParticipant
            LEFT JOIN User ON User.id = UserParticipant.User_id
    )_a1 ON _a1.participantId = Participant.id
    LEFT JOIN (
        SELECT CONCAT(Client.firstName, ' ', COALESCE(Client.lastName, '')) clientName, ClientParticipant.Participant_id participantId
        FROM ClientParticipant
            LEFT JOIN Client ON Client.id = ClientParticipant.Client_id
    )_a2 ON _a2.participantId = Participant.id
    LEFT JOIN (
        SELECT Team.name teamName, TeamParticipant.Participant_id participantId
        FROM TeamParticipant
            LEFT JOIN Team ON Team.id = TeamParticipant.Team_id
    )_a3 ON _a3.participantId = Participant.id
    LEFT JOIN Consultant ON Consultant.id = DedicatedMentor.Consultant_id
    LEFT JOIN Personnel ON Personnel.id = Consultant.Personnel_id
    CROSS JOIN EvaluationPlan
    WHERE 
        Personnel.id = :personnelId
        AND Consultant.active = true
        AND Consultant.Program_id = :programId
        AND DedicatedMentor.cancelled = false
        AND Participant.active = true
        AND EvaluationPlan.Program_id = :programId
        AND EvaluationPlan.disabled = false
        {$evaluationPlanFilterClause}
        {$participantFilterClause}
)_a
LEFT JOIN MentorEvaluationReport ON (MentorEvaluationReport.DedicatedMentor_id = _a.dedicatedMentorId AND MentorEvaluationReport.EvaluationPlan_id = _a.evaluationPlanId)
{$submittedStatusFilterClause}
ORDER BY evaluationPlanIntervalDay ASC
LIMIT {$offset}, {$pageSize}
_STATEMENT;
        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        $query->execute($params);
        $listResult = $query->fetchAll(PDO::FETCH_ASSOC);
        return [
            'total' => $this->totalEvaluationReportBelongsToPersonnel($personnelId, $programId, $evaluationReportFilter),
            'list' => $listResult,
        ];
    }

    protected function totalEvaluationReportBelongsToPersonnel(string $personnelId, string $programId,
            EvaluationReportFilter $evaluationReportFilter): ?int
    {
        $evaluationPlanFilterClause = '';
        $participantFilterClause = '';
        $submittedStatusFilterClause = '';

        $params = [
            "personnelId" => $personnelId,
            "programId" => $programId,
        ];
        if (!empty($evaluationPlanId = $evaluationReportFilter->getEvaluationPlanId())) {
            $evaluationPlanFilterClause = "AND EvaluationPlan.id = :evaluationPlanId";
            $params['evaluationPlanId'] = $evaluationPlanId;
        }
        if (!empty($participantName = $evaluationReportFilter->getParticipantName())) {
            $participantFilterClause = "HAVING participantName LIKE :participantName";
            $params['participantName'] = "%{$participantName}%";
        }
        if (!is_null($submittedStatus = $evaluationReportFilter->getSubmittedStatus())) {
            $submittedStatusFilterClause = $submittedStatus ?
                    "WHERE (MentorEvaluationReport.id IS NOT NULL AND MentorEvaluationReport.cancelled = false)" :
                    "WHERE MentorEvaluationReport.id IS NULL";
        }

        $statement = <<<_STATEMENT
SELECT COUNT(_a.dedicatedMentorId) total
FROM (
    SELECT 
        DedicatedMentor.id dedicatedMentorId, 
        Participant.id participantId,
        COALESCE(_a1.userName, _a2.clientName, _a3.teamName) participantName, 
        EvaluationPlan.id evaluationPlanId
    FROM DedicatedMentor
    LEFT JOIN Participant ON Participant.id = DedicatedMentor.Participant_id
    LEFT JOIN (
        SELECT CONCAT(User.firstName, ' ', COALESCE(User.lastName, '')) userName, UserParticipant.Participant_id participantId
        FROM UserParticipant
            LEFT JOIN User ON User.id = UserParticipant.User_id
    )_a1 ON _a1.participantId = Participant.id
    LEFT JOIN (
        SELECT CONCAT(Client.firstName, ' ', COALESCE(Client.lastName, '')) clientName, ClientParticipant.Participant_id participantId
        FROM ClientParticipant
            LEFT JOIN Client ON Client.id = ClientParticipant.Client_id
    )_a2 ON _a2.participantId = Participant.id
    LEFT JOIN (
        SELECT Team.name teamName, TeamParticipant.Participant_id participantId
        FROM TeamParticipant
            LEFT JOIN Team ON Team.id = TeamParticipant.Team_id
    )_a3 ON _a3.participantId = Participant.id
    LEFT JOIN Consultant ON Consultant.id = DedicatedMentor.Consultant_id
    LEFT JOIN Personnel ON Personnel.id = Consultant.Personnel_id
    CROSS JOIN EvaluationPlan
    WHERE 
        Personnel.id = :personnelId
        AND Consultant.active = true
        AND Consultant.Program_id = :programId
        AND DedicatedMentor.cancelled = false
        AND Participant.active = true
        AND EvaluationPlan.Program_id = :programId
        AND EvaluationPlan.disabled = false
        {$evaluationPlanFilterClause}
        {$participantFilterClause}
)_a
LEFT JOIN MentorEvaluationReport ON (MentorEvaluationReport.DedicatedMentor_id = _a.dedicatedMentorId AND MentorEvaluationReport.EvaluationPlan_id = _a.evaluationPlanId)
{$submittedStatusFilterClause}
_STATEMENT;
        $query = $this->getEntityManager()->getConnection()->prepare($statement);
        $query->execute($params);

        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result[0]["total"];
    }

    public function anEvaluationReportBelongsToPersonnel(string $personnelId, string $id): EvaluationReport
    {
        $params = [
            'personnelId' => $personnelId,
            'id' => $id,
        ];

        $qb = $this->createQueryBuilder('evaluationReport');
        $qb->select('evaluationReport')
                ->andWhere($qb->expr()->eq('evaluationReport.id', ':id'))
                ->leftJoin('evaluationReport.dedicatedMentor', 'dedicatedMentor')
                ->leftJoin('dedicatedMentor.consultant', 'consultant')
                ->leftJoin('consultant.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: evaluation report not found');
        }
    }

    public function allNonPaginatedEvaluationReportsInProgram(
            Program $program, EvaluationReportSummaryFilter $evaluationReportSummaryFilter)
    {
        $params = [
            'programId' => $program->getId(),
        ];

        $qb = $this->createQueryBuilder('evaluationReport');
        $qb->select('evaluationReport')
                ->leftJoin('evaluationReport.evaluationPlan', 'evaluationPlan')
                ->leftJoin('evaluationPlan.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameters($params);
        
        if (!empty($evaluationPlanIdList = $evaluationReportSummaryFilter->getEvaluationPlanIdList())) {
            $qb->andWhere($qb->expr()->in('evaluationPlan.id', ':evaluationPlanIdList'))
                    ->setParameter('evaluationPlanIdList', $evaluationPlanIdList);
        }
        if (!empty($participantIdList = $evaluationReportSummaryFilter->getParticipantIdList())) {
            $qb->leftJoin('evaluationReport.dedicatedMentor', 'dedicatedMentor')
                    ->leftJoin('dedicatedMentor.participant', 'participant')
                    ->andWhere($qb->expr()->in('participant.id', ':participantIdList'))
                    ->setParameter('participantIdList', $participantIdList);
        }
        return $qb->getQuery()->getResult();
    }

    public function allEvaluationReportsBelongsToParticipantInProgram(
            Program $program, string $participantId,
            EvaluationReportTranscriptFilter $evaluationReportTranscriptFilter)
    {
        $params = [
            'programId' => $program->getId(),
            'participantId' => $participantId,
        ];

        $qb = $this->createQueryBuilder('evaluationReport');
        $qb->select('evaluationReport')
                ->leftJoin('evaluationReport.dedicatedMentor', 'dedicatedMentor')
                ->leftJoin('dedicatedMentor.participant', 'participant')
                ->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameters($params);
        
        if (!empty($evaluationPlanIdList = $evaluationReportTranscriptFilter->getEvaluationPlanIdList())) {
            $qb->leftJoin('evaluationReport.evaluationPlan', 'evaluationPlan')
                    ->andWhere($qb->expr()->in('evaluationPlan.id', ':evaluationPlanIdList'))
                    ->setParameter('evaluationPlanIdList', $evaluationPlanIdList);
        }
        if (!empty($mentorIdList = $evaluationReportTranscriptFilter->getMentorIdList())) {
            $qb->leftJoin('dedicatedMentor.consultant', 'consultant')
                    ->andWhere($qb->expr()->in('consultant.id', ':mentorIdList'))
                    ->setParameter('mentorIdList', $mentorIdList);
        }
        return $qb->getQuery()->getResult();
    }

}
