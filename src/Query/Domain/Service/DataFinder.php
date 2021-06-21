<?php

namespace Query\Domain\Service;

use Doctrine\DBAL\Connection;
use PDO;

class DataFinder
{

    /**
     * 
     * @var Connection
     */
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    public function summaryOfParticipant(string $participantId): array
    {
        $statement = <<<_STATEMENT
SELECT 
    _a.participantId, 
    _a.participantRating,
    _a.totalCompletedMission,
    _b.totalMission,
    _a.lastCompletedTime,
    _a.lastMissionId,
    _a.lastMissionName,
    _a.achievement,
    _a.completedMetric,
    _a.totalAssignedMetric,
    _a.reportId
FROM (
    SELECT 
        Participant.id participantId , 
        __a.totalCompletedMission, 
        __a.lastCompletedTime, 
        __a.Mission_id lastMissionId, 
        __b.participantRating,
        __c.reportId,
        __c.achievement,
        __c.completedMetric,
        __c.totalAssignedMetric,
        Mission.name lastMissionName,
        Participant.Program_id programId
    FROM Participant
    LEFT OUTER JOIN (
        SELECT CM.Participant_id, ___a.totalCompletedMission, ___a.lastCompletedTime, CM.Mission_id
        FROM (
            SELECT Participant_id, COUNT(id) totalCompletedMission, MAX(completedTime) lastCompletedTime
            FROM CompletedMission
            GROUP BY Participant_id
        )___a
        LEFT JOIN CompletedMission CM ON CM.completedTime = ___a.lastCompletedTime AND CM.Participant_id = ___a.Participant_id
    )__a ON __a.Participant_id = Participant.id
    LEFT JOIN (
        SELECT ConsultationSession.Participant_id, AVG(ConsultantFeedback.participantRating) participantRating
        FROM ConsultantFeedback
        LEFT JOIN ConsultationSession ON ConsultationSession.id = ConsultantFeedback.ConsultationSession_id
        GROUP BY Participant_id
    )__b ON __b.Participant_id = Participant.id
    LEFT JOIN (
        SELECT MetricAssignment.Participant_id,
            __c_b.id reportId,
            SUM(__c_d.inputValue/__c_c.target)/COUNT(__c_c.target) achievement,
            SUM(CASE WHEN __c_d.inputValue >= __c_c.target THEN 1 ELSE 0 END) completedMetric,
            COUNT(__c_c.target) totalAssignedMetric
        FROM (
            SElECT MetricAssignment_id, MAX(observationTime) observationTime
            FROM MetricAssignmentReport
            WHERE approved = true AND removed = false
            GROUP BY MetricAssignment_id
        )__c_a INNER JOIN MetricAssignmentReport __c_b USING (MetricAssignment_id, observationTime)
        LEFT JOIN MetricAssignment ON MetricAssignment.id = __c_b.MetricAssignment_id
        LEFT JOIN (
            SELECT id, `target`, MetricAssignment_id
            FROM AssignmentField
            WHERE disabled = false
        )__c_c ON __c_c.MetricAssignment_id = __c_b.MetricAssignment_id
        LEFT JOIN (
            SELECT inputValue, MetricAssignmentReport_id, AssignmentField_id
            FROM AssignmentFieldValue
            WHERE removed = false
        )__c_d ON __c_d.MetricAssignmentReport_id = __c_b.id AND __c_d.AssignmentField_id = __c_c.id
        WHERE __c_b.approved = true AND __c_b.removed = false
        GROUP BY reportId
    )__c ON __c.Participant_id = Participant.id
    LEFT JOIN Mission ON Mission.id = __a.Mission_id
    WHERE Participant.active = true
        AND Participant.id = :participantId
)_a
LEFT JOIN (
    SELECT COUNT(*) totalMission, Mission.Program_id
    FROM Mission
    WHERE Mission.Published = true
    GROUP BY Program_id
)_b ON _b.Program_id = _a.programId
LIMIT 1
_STATEMENT;
        $query = $this->connection->prepare($statement);
        $query->execute(['participantId' => $participantId]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }
    
    public function summaryOfAllClientProgramParticipations(string $clientId, int $page, int $pageSize): array
    {
        $offset = $pageSize * ($page - 1);
        $statement = <<<_STATEMENT
SELECT 
    _a.programId,
    _a.programName,
    _a.programStrictMissionOrder,
    _b.participantId, 
    _b.participantRating,
    _b.totalCompletedMission,
    _c.totalMission,
    _b.lastCompletedTime,
    _b.lastMissionId,
    _b.lastMissionName,
    _b.achievement,
    _b.completedMetric,
    _b.totalAssignedMetric,
    _b.reportId
FROM (
    SELECT Program.id programId, Program.name programName, Program.strictMissionOrder programStrictMissionOrder, Participant.id participantId
    FROM ClientParticipant
    LEFT JOIN Client ON Client.id = ClientParticipant.Client_id
    LEFT JOIN Participant ON Participant.id = ClientParticipant.Participant_id
    LEFT JOIN Program ON Program.id = Participant.Program_id
    WHERE Participant.active = true
        AND Client.id = :clientId
)_a
LEFT JOIN (
    SELECT 
        Participant.id participantId , 
        __a.totalCompletedMission, 
        __a.lastCompletedTime, 
        __a.Mission_id lastMissionId, 
        __b.participantRating,
        __c.reportId,
        __c.achievement,
        __c.completedMetric,
        __c.totalAssignedMetric,
        Mission.name lastMissionName,
        Participant.Program_id programId
    FROM Participant
    LEFT OUTER JOIN (
        SELECT CM.Participant_id, ___a.totalCompletedMission, ___a.lastCompletedTime, CM.Mission_id
        FROM (
            SELECT Participant_id, COUNT(id) totalCompletedMission, MAX(completedTime) lastCompletedTime
            FROM CompletedMission
            GROUP BY Participant_id
        )___a
        LEFT JOIN CompletedMission CM ON CM.completedTime = ___a.lastCompletedTime AND CM.Participant_id = ___a.Participant_id
    )__a ON __a.Participant_id = Participant.id
    LEFT JOIN (
        SELECT ConsultationSession.Participant_id, AVG(ConsultantFeedback.participantRating) participantRating
        FROM ConsultantFeedback
        LEFT JOIN ConsultationSession ON ConsultationSession.id = ConsultantFeedback.ConsultationSession_id
        GROUP BY Participant_id
    )__b ON __b.Participant_id = Participant.id
    LEFT JOIN (
        SELECT MetricAssignment.Participant_id,
            __c_b.id reportId,
            SUM(__c_d.inputValue/__c_c.target)/COUNT(__c_c.target) achievement,
            SUM(CASE WHEN __c_d.inputValue >= __c_c.target THEN 1 ELSE 0 END) completedMetric,
            COUNT(__c_c.target) totalAssignedMetric
        FROM (
            SElECT MetricAssignment_id, MAX(observationTime) observationTime
            FROM MetricAssignmentReport
            WHERE approved = true AND removed = false
            GROUP BY MetricAssignment_id
        )__c_a INNER JOIN MetricAssignmentReport __c_b USING (MetricAssignment_id, observationTime)
        LEFT JOIN MetricAssignment ON MetricAssignment.id = __c_b.MetricAssignment_id
        LEFT JOIN (
            SELECT id, `target`, MetricAssignment_id
            FROM AssignmentField
            WHERE disabled = false
        )__c_c ON __c_c.MetricAssignment_id = __c_b.MetricAssignment_id
        LEFT JOIN (
            SELECT inputValue, MetricAssignmentReport_id, AssignmentField_id
            FROM AssignmentFieldValue
            WHERE removed = false
        )__c_d ON __c_d.MetricAssignmentReport_id = __c_b.id AND __c_d.AssignmentField_id = __c_c.id
        WHERE __c_b.approved = true AND __c_b.removed = false
        GROUP BY reportId
    )__c ON __c.Participant_id = Participant.id
    LEFT JOIN Mission ON Mission.id = __a.Mission_id
    WHERE Participant.active = true
)_b ON _b.participantId = _a.participantId
LEFT JOIN (
    SELECT COUNT(*) totalMission, Mission.Program_id programId
    FROM Mission
    WHERE Mission.Published = true
    GROUP BY programId
)_c ON _c.programId = _a.programId
ORDER BY _b.totalCompletedMission DESC
LIMIT {$offset}, {$pageSize}
_STATEMENT;
        $query = $this->connection->prepare($statement);
        $params = [
            "clientId" => $clientId,
        ];
        $query->execute($params);
        $result = [];
        $result['list'] = $query->fetchAll(PDO::FETCH_ASSOC);
        $result['total'] = $this->totalActiveClientProgramParticipation($clientId);
        return $result;
    }
    public function totalActiveClientProgramParticipation(string $clientId): int
    {
        $statement = <<<_STATEMENT
SELECT COUNT(ClientParticipant.id) total
FROM ClientParticipant
LEFT JOIN Client ON Client.id = ClientParticipant.Client_id
LEFT JOIN Participant ON Participant.id = ClientParticipant.Participant_id
WHERE Participant.active = true
    AND Client.id = :clientId
_STATEMENT;
        $query = $this->connection->prepare($statement);
        $params = [
            "clientId" => $clientId,
        ];
        $query->execute($params);

        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result[0]["total"];
    }
    
    public function summaryOfAllUserProgramParticipations(string $userId, int $page, int $pageSize): array
    {
        $offset = $pageSize * ($page - 1);
        $statement = <<<_STATEMENT
SELECT 
    _a.programId,
    _a.programName,
    _a.programStrictMissionOrder,
    _b.participantId, 
    _b.participantRating,
    _b.totalCompletedMission,
    _c.totalMission,
    _b.lastCompletedTime,
    _b.lastMissionId,
    _b.lastMissionName,
    _b.achievement,
    _b.completedMetric,
    _b.totalAssignedMetric,
    _b.reportId
FROM (
    SELECT Program.id programId, Program.name programName, Program.strictMissionOrder programStrictMissionOrder, Participant.id participantId
    FROM UserParticipant
    LEFT JOIN User ON User.id = UserParticipant.User_id
    LEFT JOIN Participant ON Participant.id = UserParticipant.Participant_id
    LEFT JOIN Program ON Program.id = Participant.Program_id
    WHERE Participant.active = true
        AND User.id = :userId
)_a
LEFT JOIN (
    SELECT 
        Participant.id participantId , 
        __a.totalCompletedMission, 
        __a.lastCompletedTime, 
        __a.Mission_id lastMissionId, 
        __b.participantRating,
        __c.reportId,
        __c.achievement,
        __c.completedMetric,
        __c.totalAssignedMetric,
        Mission.name lastMissionName,
        Participant.Program_id programId
    FROM Participant
    LEFT OUTER JOIN (
        SELECT CM.Participant_id, ___a.totalCompletedMission, ___a.lastCompletedTime, CM.Mission_id
        FROM (
            SELECT Participant_id, COUNT(id) totalCompletedMission, MAX(completedTime) lastCompletedTime
            FROM CompletedMission
            GROUP BY Participant_id
        )___a
        LEFT JOIN CompletedMission CM ON CM.completedTime = ___a.lastCompletedTime AND CM.Participant_id = ___a.Participant_id
    )__a ON __a.Participant_id = Participant.id
    LEFT JOIN (
        SELECT ConsultationSession.Participant_id, AVG(ConsultantFeedback.participantRating) participantRating
        FROM ConsultantFeedback
        LEFT JOIN ConsultationSession ON ConsultationSession.id = ConsultantFeedback.ConsultationSession_id
        GROUP BY Participant_id
    )__b ON __b.Participant_id = Participant.id
    LEFT JOIN (
        SELECT MetricAssignment.Participant_id,
            __c_b.id reportId,
            SUM(__c_d.inputValue/__c_c.target)/COUNT(__c_c.target) achievement,
            SUM(CASE WHEN __c_d.inputValue >= __c_c.target THEN 1 ELSE 0 END) completedMetric,
            COUNT(__c_c.target) totalAssignedMetric
        FROM (
            SElECT MetricAssignment_id, MAX(observationTime) observationTime
            FROM MetricAssignmentReport
            WHERE approved = true AND removed = false
            GROUP BY MetricAssignment_id
        )__c_a INNER JOIN MetricAssignmentReport __c_b USING (MetricAssignment_id, observationTime)
        LEFT JOIN MetricAssignment ON MetricAssignment.id = __c_b.MetricAssignment_id
        LEFT JOIN (
            SELECT id, `target`, MetricAssignment_id
            FROM AssignmentField
            WHERE disabled = false
        )__c_c ON __c_c.MetricAssignment_id = __c_b.MetricAssignment_id
        LEFT JOIN (
            SELECT inputValue, MetricAssignmentReport_id, AssignmentField_id
            FROM AssignmentFieldValue
            WHERE removed = false
        )__c_d ON __c_d.MetricAssignmentReport_id = __c_b.id AND __c_d.AssignmentField_id = __c_c.id
        WHERE __c_b.approved = true AND __c_b.removed = false
        GROUP BY reportId
    )__c ON __c.Participant_id = Participant.id
    LEFT JOIN Mission ON Mission.id = __a.Mission_id
    WHERE Participant.active = true
)_b ON _b.participantId = _a.participantId
LEFT JOIN (
    SELECT COUNT(*) totalMission, Mission.Program_id programId
    FROM Mission
    WHERE Mission.Published = true
    GROUP BY programId
)_c ON _c.programId = _a.programId
ORDER BY _b.totalCompletedMission DESC
LIMIT {$offset}, {$pageSize}
_STATEMENT;
        $query = $this->connection->prepare($statement);
        $params = [
            "userId" => $userId,
        ];
        $query->execute($params);
        $result = [];
        $result['list'] = $query->fetchAll(PDO::FETCH_ASSOC);
        $result['total'] = $this->totalActiveUserProgramParticipation($userId);
        return $result;
    }
    public function totalActiveUserProgramParticipation(string $userId): int
    {
        $statement = <<<_STATEMENT
SELECT COUNT(UserParticipant.id) total
FROM UserParticipant
LEFT JOIN User ON User.id = UserParticipant.User_id
LEFT JOIN Participant ON Participant.id = UserParticipant.Participant_id
WHERE Participant.active = true
    AND User.id = :userId
_STATEMENT;
        $query = $this->connection->prepare($statement);
        $params = [
            "userId" => $userId,
        ];
        $query->execute($params);

        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result[0]["total"];
    }
    
    public function summaryOfAllTeamProgramParticipations(string $teamId, int $page, int $pageSize): array
    {
        $offset = $pageSize * ($page - 1);
        $statement = <<<_STATEMENT
SELECT 
    _a.programId,
    _a.programName,
    _a.programStrictMissionOrder,
    _b.participantId, 
    _b.participantRating,
    _b.totalCompletedMission,
    _c.totalMission,
    _b.lastCompletedTime,
    _b.lastMissionId,
    _b.lastMissionName,
    _b.achievement,
    _b.completedMetric,
    _b.totalAssignedMetric,
    _b.reportId
FROM (
    SELECT Program.id programId, Program.name programName, Program.strictMissionOrder programStrictMissionOrder, Participant.id participantId
    FROM TeamParticipant
    LEFT JOIN Team ON Team.id = TeamParticipant.Team_id
    LEFT JOIN Participant ON Participant.id = TeamParticipant.Participant_id
    LEFT JOIN Program ON Program.id = Participant.Program_id
    WHERE Participant.active = true
        AND Team.id = :teamId
)_a
LEFT JOIN (
    SELECT 
        Participant.id participantId , 
        __a.totalCompletedMission, 
        __a.lastCompletedTime, 
        __a.Mission_id lastMissionId, 
        __b.participantRating,
        __c.reportId,
        __c.achievement,
        __c.completedMetric,
        __c.totalAssignedMetric,
        Mission.name lastMissionName,
        Participant.Program_id programId
    FROM Participant
    LEFT OUTER JOIN (
        SELECT CM.Participant_id, ___a.totalCompletedMission, ___a.lastCompletedTime, CM.Mission_id
        FROM (
            SELECT Participant_id, COUNT(id) totalCompletedMission, MAX(completedTime) lastCompletedTime
            FROM CompletedMission
            GROUP BY Participant_id
        )___a
        LEFT JOIN CompletedMission CM ON CM.completedTime = ___a.lastCompletedTime AND CM.Participant_id = ___a.Participant_id
    )__a ON __a.Participant_id = Participant.id
    LEFT JOIN (
        SELECT ConsultationSession.Participant_id, AVG(ConsultantFeedback.participantRating) participantRating
        FROM ConsultantFeedback
        LEFT JOIN ConsultationSession ON ConsultationSession.id = ConsultantFeedback.ConsultationSession_id
        GROUP BY Participant_id
    )__b ON __b.Participant_id = Participant.id
    LEFT JOIN (
        SELECT MetricAssignment.Participant_id,
            __c_b.id reportId,
            SUM(__c_d.inputValue/__c_c.target)/COUNT(__c_c.target) achievement,
            SUM(CASE WHEN __c_d.inputValue >= __c_c.target THEN 1 ELSE 0 END) completedMetric,
            COUNT(__c_c.target) totalAssignedMetric
        FROM (
            SElECT MetricAssignment_id, MAX(observationTime) observationTime
            FROM MetricAssignmentReport
            WHERE approved = true AND removed = false
            GROUP BY MetricAssignment_id
        )__c_a INNER JOIN MetricAssignmentReport __c_b USING (MetricAssignment_id, observationTime)
        LEFT JOIN MetricAssignment ON MetricAssignment.id = __c_b.MetricAssignment_id
        LEFT JOIN (
            SELECT id, `target`, MetricAssignment_id
            FROM AssignmentField
            WHERE disabled = false
        )__c_c ON __c_c.MetricAssignment_id = __c_b.MetricAssignment_id
        LEFT JOIN (
            SELECT inputValue, MetricAssignmentReport_id, AssignmentField_id
            FROM AssignmentFieldValue
            WHERE removed = false
        )__c_d ON __c_d.MetricAssignmentReport_id = __c_b.id AND __c_d.AssignmentField_id = __c_c.id
        WHERE __c_b.approved = true AND __c_b.removed = false
        GROUP BY reportId
    )__c ON __c.Participant_id = Participant.id
    LEFT JOIN Mission ON Mission.id = __a.Mission_id
    WHERE Participant.active = true
)_b ON _b.participantId = _a.participantId
LEFT JOIN (
    SELECT COUNT(*) totalMission, Mission.Program_id programId
    FROM Mission
    WHERE Mission.Published = true
    GROUP BY programId
)_c ON _c.programId = _a.programId
ORDER BY _b.totalCompletedMission DESC
LIMIT {$offset}, {$pageSize}
_STATEMENT;
        $query = $this->connection->prepare($statement);
        $params = [
            "teamId" => $teamId,
        ];
        $query->execute($params);
        $result = [];
        $result['list'] = $query->fetchAll(PDO::FETCH_ASSOC);
        $result['total'] = $this->totalActiveTeamProgramParticipation($teamId);
        return $result;
    }
    public function totalActiveTeamProgramParticipation(string $teamId): int
    {
        $statement = <<<_STATEMENT
SELECT COUNT(TeamParticipant.id) total
FROM TeamParticipant
LEFT JOIN Team ON Team.id = TeamParticipant.Team_id
LEFT JOIN Participant ON Participant.id = TeamParticipant.Participant_id
WHERE Participant.active = true
    AND Team.id = :teamId
_STATEMENT;
        $query = $this->connection->prepare($statement);
        $params = [
            "teamId" => $teamId,
        ];
        $query->execute($params);

        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result[0]["total"];
    }
    
}
