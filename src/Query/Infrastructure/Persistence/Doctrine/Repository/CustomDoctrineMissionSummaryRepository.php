<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityManager;
use Query\Domain\Task\Dependency\Firm\Program\Participant\MissionSummaryRepository;

class CustomDoctrineMissionSummaryRepository implements MissionSummaryRepository
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

    public function ofParticipantId(string $participantId): ?array
    {
        $parameters = ['participantId' => $participantId];
        $statement = <<<_STATEMENT
SELECT 
    _a.totalCompletedMission,
    _b.totalMission,
    _a.lastCompletedTime,
    M1.id lastCompletedMissionId,
    M1.name lastCompletedMissionName,
    M2.id nextMissionId,
    M2.name nextMissionName
FROM (
        SELECT 
            COUNT(DISTINCT CM.Mission_id) totalCompletedMission, 
            MAX(CM.completedTime) lastCompletedTime, 
            CM.Participant_id
        FROM CompletedMission AS CM
            LEFT JOIN Mission ON Mission.id = CM.Mission_id
        WHERE CM.Participant_id = :participantId
            AND Mission.published = true
    )_a
    LEFT JOIN CompletedMission AS CM2 ON CM2.Participant_id = _a.Participant_id 
        AND CM2.completedTime = _a.lastCompletedTime
    LEFT JOIN Mission AS M1 ON M1.id = CM2.Mission_id
    LEFT JOIN Mission AS M2 ON M2.Program_id = M1.Program_id AND M2.position > M1.position AND M2.published = true
    LEFT JOIN Mission AS M3 ON M3.Program_id = M1.Program_id AND M3.position > M1.position AND M3.position < M2.position AND M3.published = true
    LEFT JOIN (
        SELECT COUNT(Mission.id) totalMission, Mission.Program_id
        FROM Mission
        WHERE Mission.Published = true
        GROUP BY Program_id
    )_b ON _b.Program_id = M1.Program_id
    LEFT JOIN (
        SELECT COUNT(MetricAssignment.id) totalAssignedMetric, Participant_id
        FROM MetricAssignment
        GROUP BY Participant_id
    )_c ON _c.Participant_id = _a.Participant_id
WHERE M3.id IS NULL
LIMIT 1
_STATEMENT;
        $query = $this->em->getConnection()->prepare($statement);
        $result = $query->executeQuery($parameters)->fetchAssociative();
        return $result ?: null;
    }

}
