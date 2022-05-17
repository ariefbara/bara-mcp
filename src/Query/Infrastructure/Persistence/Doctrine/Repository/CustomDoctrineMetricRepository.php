<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityManager;
use Query\Domain\Task\Dependency\MetricRepository;

class CustomDoctrineMetricRepository implements MetricRepository
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

    public function metricSummaryOfParticipant(string $participantId): ?array
    {
        $parameters = [
            "participantId" => $participantId,
        ];
        
        $statement = <<<_STATEMENT
SELECT 
    MetricAssignment.startDate,
    MetricAssignment.endDate,
    Metric.name metricName,
    AssignmentField.target,
    _a.id lastApprovedReportId,
    _a.inputValue lastApprovedReportValue,
    _a.observationTime lastApprovedObservationTime,
    _b.id lastUnapprovedReportId,
    _b.inputValue lastUnapprovedReportValue,
    _b.observationTime lastUnapprovedObservationTime
FROM MetricAssignment
LEFT JOIN AssignmentField ON AssignmentField.MetricAssignment_id = MetricAssignment.id
LEFT JOIN Metric ON Metric.id = AssignmentField.Metric_id
LEFT JOIN (
    SELECT _a1.observationTime, _a1.id, AssignmentFieldValue.inputValue, AssignmentFieldValue.AssignmentField_id
    FROM (
        SELECT MetricAssignmentReport.observationTime, MetricAssignmentReport.id 
        FROM (
            SELECT 
                MAX(MetricAssignmentReport.observationTime) lastApprovedObservationTime, 
                MetricAssignmentReport.MetricAssignment_id metricAssignmentId
            FROM MetricAssignmentReport
            LEFT JOIN MetricAssignment ON MetricAssignment.id = MetricAssignmentReport.MetricAssignment_id
            LEFT JOIN Participant ON Participant.id = MetricAssignment.Participant_id
            WHERE MetricAssignmentReport.approved = true 
                AND MetricAssignmentReport.removed = false 
                AND Participant.id=:participantId
            GROUP BY MetricAssignmentReport.MetricAssignment_id
        )_a1a
        LEFT JOIN  MetricAssignmentReport 
            ON MetricAssignmentReport.observationTime = _a1a.lastApprovedObservationTime
            AND MetricAssignmentReport.MetricAssignment_id = _a1a.metricAssignmentId
            AND MetricAssignmentReport.approved = true AND MetricAssignmentReport.removed = false
        LIMIT 1
    )_a1
    LEFT JOIN AssignmentFieldValue ON AssignmentFieldValue.MetricAssignmentReport_id = _a1.id
    WHERE AssignmentFieldValue.removed = false
)_a ON _a.AssignmentField_id = AssignmentField.id
LEFT JOIN (
    SELECT _b1.observationTime, _b1.id, AssignmentFieldValue.inputValue, AssignmentFieldValue.AssignmentField_id
    FROM (
        SELECT MetricAssignmentReport.observationTime, MetricAssignmentReport.id
        FROM (
            SELECT 
                MAX(MetricAssignmentReport.observationTime) lastApprovedObservationTime, 
                MetricAssignmentReport.MetricAssignment_id metricAssignmentId
            FROM MetricAssignmentReport
            LEFT JOIN MetricAssignment ON MetricAssignment.id = MetricAssignmentReport.MetricAssignment_id
            LEFT JOIN Participant ON Participant.id = MetricAssignment.Participant_id
            WHERE MetricAssignmentReport.approved IS NULL 
                AND MetricAssignmentReport.removed = false
                AND Participant.id=:participantId
            GROUP BY MetricAssignmentReport.MetricAssignment_id
        )_b1a
        LEFT JOIN  MetricAssignmentReport 
            ON MetricAssignmentReport.observationTime = _b1a.lastApprovedObservationTime
            AND MetricAssignmentReport.MetricAssignment_id = _b1a.metricAssignmentId
            AND MetricAssignmentReport.approved IS NULL AND MetricAssignmentReport.removed = false
        LIMIT 1
    )_b1
    LEFT JOIN AssignmentFieldValue ON AssignmentFieldValue.MetricAssignmentReport_id = _b1.id
    WHERE AssignmentFieldValue.removed = false
)_b ON _b.AssignmentField_id = AssignmentField.id AND (_b.observationTime > _a.observationTime OR _a.observationTime IS NULL)
WHERE MetricAssignment.Participant_id = :participantId
    AND AssignmentField.disabled = false        
_STATEMENT;
        $query = $this->em->getConnection()->prepare($statement);
        return $query->executeQuery($parameters)->fetchAllAssociative();
    }

}
