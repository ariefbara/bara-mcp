<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityManager;
use PDO;
use Query\Application\Service\Firm\Program\ParticipantSummaryRepository;

class DoctrineParticipantSummaryRepository implements ParticipantSummaryRepository
{

    /**
     *
     * @var EntityManager
     */
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function allParticipantsSummaryInProgram(string $programId, int $page, int $pageSize, ?string $searchByParticipantName): array
    {
        $offset = $pageSize * ($page - 1);
        
        $params = [
            "programId" => $programId,
        ];
        
        $searchByParticipantNameClause = "";
        if (!empty($searchByParticipantName)) {
            $searchByParticipantNameClause = "HAVING participantName LIKE :participantName";
            $params['participantName'] = "%$searchByParticipantName%";
        }
        
        $statement = <<<_STATEMENT
SELECT 
    _a.participantId participantId, 
    COALESCE(_c.userName, _d.clientName, _e.teamName) participantName, 
    _a.participantRating,
    _a.totalCompletedMission,
    _b.totalMission,
    _a.lastCompletedTime,
    _a.lastMissionId,
    _a.lastMissionName
FROM (
    SELECT 
        Participant.id participantId , 
        __b.totalCompletedMission, 
        __b.lastCompletedTime, 
        __b.Mission_id lastMissionId, 
        __a.participantRating,
        Mission.name lastMissionName,
        Participant.Program_id programId
    FROM Participant
    LEFT OUTER JOIN (
        SELECT CM.Participant_id, ___b.totalCompletedMission, ___b.lastCompletedTime, CM.Mission_id
        FROM (
            SELECT Participant_id, COUNT(DISTINCT Mission_id) totalCompletedMission, MAX(completedTime) lastCompletedTime
            FROM CompletedMission
            GROUP BY Participant_id
        )___b
        LEFT JOIN CompletedMission CM ON CM.completedTime = ___b.lastCompletedTime AND CM.Participant_id = ___b.Participant_id
    )__b ON __b.Participant_id = Participant.id
    LEFT JOIN (
        SELECT ConsultationSession.Participant_id, AVG(ConsultantFeedback.participantRating) participantRating
        FROM ConsultantFeedback
        LEFT JOIN ConsultationSession ON ConsultationSession.id = ConsultantFeedback.ConsultationSession_id
        GROUP BY Participant_id
    )__a ON __a.Participant_id = Participant.id
    LEFT JOIN Mission ON Mission.id = __b.Mission_id
    WHERE Participant.active = true
        AND Participant.Program_id=  :programId
)_a
LEFT JOIN (
    SELECT COUNT(*) totalMission, Mission.Program_id programId
    FROM Mission
    WHERE Mission.Published = true
    GROUP BY programId
)_b ON _b.programId = _a.programId
LEFT JOIN (
    SELECT CONCAT(User.firstName, ' ', COALESCE(User.lastName, '')) userName, UserParticipant.Participant_id participantId
    FROM UserParticipant
        LEFT JOIN User ON User.id = UserParticipant.User_id
)_c ON _c.participantId = _a.participantId
LEFT JOIN (
    SELECT CONCAT(Client.firstName, ' ', COALESCE(Client.lastName, '')) clientName, ClientParticipant.Participant_id participantId
    FROM ClientParticipant
        LEFT JOIN Client ON Client.id = ClientParticipant.Client_id
)_d ON _d.participantId = _a.participantId
LEFT JOIN (
    SELECT Team.name teamName, TeamParticipant.Participant_id participantId
    FROM TeamParticipant
        LEFT JOIN Team ON Team.id = TeamParticipant.Team_id
)_e ON _e.participantId = _a.participantId
$searchByParticipantNameClause
ORDER BY _a.totalCompletedMission DESC
LIMIT {$offset}, {$pageSize}
_STATEMENT;
        $query = $this->em->getConnection()->prepare($statement);
        $query->execute($params);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalActiveParticipantInProgram(string $programId, ?string $searchByParticipantName): int
    {
        $params = [
            "programId" => $programId,
        ];
        
        $searchByParticipantNameClause = "";
        if (!empty($searchByParticipantName)) {
            $searchByParticipantNameClause = "HAVING participantName LIKE :participantName";
            $params['participantName'] = "%$searchByParticipantName%";
        }
        
        $statement = <<<_STATEMENT
SELECT COUNT(_a.participantId) total
FROM (
    SELECT Participant.id participantId, COALESCE(_c.userName, _d.clientName, _e.teamName) participantName
    FROM Participant
    LEFT JOIN (
        SELECT CONCAT(User.firstName, ' ', COALESCE(User.lastName, '')) userName, UserParticipant.Participant_id participantId
        FROM UserParticipant
            LEFT JOIN User ON User.id = UserParticipant.User_id
    )_c ON _c.participantId = Participant.id
    LEFT JOIN (
        SELECT CONCAT(Client.firstName, ' ', COALESCE(Client.lastName, '')) clientName, ClientParticipant.Participant_id participantId
        FROM ClientParticipant
            LEFT JOIN Client ON Client.id = ClientParticipant.Client_id
    )_d ON _d.participantId = Participant.id
    LEFT JOIN (
        SELECT Team.name teamName, TeamParticipant.Participant_id participantId
        FROM TeamParticipant
            LEFT JOIN Team ON Team.id = TeamParticipant.Team_id
    )_e ON _e.participantId = Participant.id
    WHERE Participant.Program_id = :programId
        AND Participant.active = true
    GROUP BY Participant.id
    $searchByParticipantNameClause
)_a
_STATEMENT;
        $query = $this->em->getConnection()->prepare($statement);
        $query->execute($params);

        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result[0]["total"];
    }

    public function allParticipantAchievmentSummaryInProgram(
        string $firmId, string $programId, int $page, int $pageSize, string $orderType = "DESC"): array
    {
        $offset = $pageSize * ($page - 1);

        $statement = <<<_STATEMENT
SELECT 
    Participant.id participantId, 
    COALESCE(_b.userName, _c.clientName, _d.teamName) participantName,
    ROUND (_a.achievement * 100) achievement,
    _a.completedMetric,
    _a.totalAssignedMetric
FROM  Participant
LEFT JOIN Program ON Program.id = Participant.Program_id
LEFT OUTER JOIN (
    SELECT MetricAssignment.Participant_id,
        __b.id reportId,
        SUM(__d.inputValue/__c.target)/COUNT(__c.target) achievement,
        SUM(CASE WHEN __d.inputValue >= __c.target THEN 1 ELSE 0 END) completedMetric,
        COUNT(__c.target) totalAssignedMetric
    FROM (
        SElECT MetricAssignment_id, MAX(observationTime) observationTime
        FROM MetricAssignmentReport
        WHERE approved = true AND removed = false
        GROUP BY MetricAssignment_id
    )__a INNER JOIN MetricAssignmentReport __b USING (MetricAssignment_id, observationTime)
    LEFT JOIN MetricAssignment ON MetricAssignment.id = __b.MetricAssignment_id
    LEFT JOIN (
        SELECT id, `target`, MetricAssignment_id
        FROM AssignmentField
        WHERE disabled = false
    )__c ON __c.MetricAssignment_id = __b.MetricAssignment_id
    LEFT JOIN (
        SELECT inputValue, MetricAssignmentReport_id, AssignmentField_id
        FROM AssignmentFieldValue
        WHERE removed = false
    )__d ON __d.MetricAssignmentReport_id = __b.id AND __d.AssignmentField_id = __c.id
    WHERE __b.approved = true AND __b.removed = false
    GROUP BY reportId
)_a ON _a.Participant_id = Participant.id
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
WHERE Program.Firm_id = :firmId
    AND Program.id = :programId
    AND Participant.active= true
ORDER BY achievement {$orderType}
LIMIT {$offset}, {$pageSize}
_STATEMENT;

        $query = $this->em->getConnection()->prepare($statement);
        $params = [
            "firmId" => $firmId,
            "programId" => $programId,
        ];
        $query->execute($params);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function allParticipantEvaluationSummaryInProgram(string $firmId, string $programId, int $page, int $pageSize): array
    {
        $offset = $pageSize * ($page - 1);

        $statement = <<<_STATEMENT
SELECT _a.participantId, 
    COALESCE(_d.userName, _e.clientName, _f.teamName) participantName,
    _b.id evaluationPlanId, 
    _b.name evaluationPlanName, 
    DATE_ADD(DATE(_a.enrolledTime), INTERVAL _b.days_interval DAY) scheduledEvaluation,
    _c.extendDays
FROM (
    SELECT __a.Program_id, __a.id participantId, __a.active, __a.enrolledTime, MIN(__b.days_interval) days_interval
    FROM Participant __a
    CROSS JOIN EvaluationPlan __b ON __b.Program_id = __a.Program_id
    LEFT JOIN Evaluation __c ON __c.Participant_id = __a.id AND __c.EvaluationPlan_id = __b.id
    WHERE (__c.id IS NULL OR __c.c_status = 'extend') AND __b.disabled = false
    GROUP BY participantId
)_a
LEFT JOIN EvaluationPlan _b ON _b.days_interval = _a.days_interval AND _b.Program_id = _a.Program_id
LEFT JOIN Evaluation _c ON _c.Participant_id = _a.participantId AND _c.EvaluationPlan_id = _b.id
LEFT JOIN (
    SELECT CONCAT(User.firstName, ' ', COALESCE(User.lastName, '')) userName, UserParticipant.Participant_id participantId
    FROM UserParticipant
        LEFT JOIN User ON User.id = UserParticipant.User_id
)_d ON _d.participantId = _a.participantId
LEFT JOIN (
    SELECT CONCAT(Client.firstName, ' ', COALESCE(Client.lastName, '')) clientName, ClientParticipant.Participant_id participantId
    FROM ClientParticipant
        LEFT JOIN Client ON Client.id = ClientParticipant.Client_id
)_e ON _e.participantId = _a.participantId
LEFT JOIN (
    SELECT Team.name teamName, TeamParticipant.Participant_id participantId
    FROM TeamParticipant
        LEFT JOIN Team ON Team.id = TeamParticipant.Team_id
)_f ON _f.participantId = _a.participantId
LEFT JOIN Program _g ON _g.id = _a.Program_id
WHERE _g.Firm_id = :firmId
    AND _g.id = :programId
    AND _a.active= true
    AND _b.disabled = false
ORDER BY scheduledEvaluation ASC
LIMIT {$offset}, {$pageSize}
_STATEMENT;

        $query = $this->em->getConnection()->prepare($statement);
        $params = [
            "firmId" => $firmId,
            "programId" => $programId,
        ];
        $query->execute($params);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

}
