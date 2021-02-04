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
    _a.lastMissionName
FROM (
    SELECT 
        Participant.id participantId , 
        __a.totalCompletedMission, 
        __a.lastCompletedTime, 
        __a.Mission_id lastMissionId, 
        __b.participantRating,
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

}
