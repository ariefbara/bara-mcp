<?php

namespace Participant\Application\Service\UserParticipant\MetricAssignment;

use DateTimeImmutable;
use Participant\{
    Application\Service\UserParticipant\MetricAssignmentRepository,
    Domain\Model\Participant\MetricAssignment\MetricAssignmentReportData
};

class SubmitMetricAssignmentReport
{

    /**
     *
     * @var MetricAssignmentReportRepository
     */
    protected $metricAssignmentReportRepository;

    /**
     *
     * @var MetricAssignmentRepository
     */
    protected $metricAssignmentRepository;

    public function __construct(MetricAssignmentReportRepository $metricAssignmentReportRepository,
            MetricAssignmentRepository $metricAssignmentRepository)
    {
        $this->metricAssignmentReportRepository = $metricAssignmentReportRepository;
        $this->metricAssignmentRepository = $metricAssignmentRepository;
    }

    public function execute(
            string $userId, string $metricAssignmentId, DateTimeImmutable $observeTime,
            MetricAssignmentReportData $metricAssignmentReportData): string
    {
        $id = $this->metricAssignmentReportRepository->nextIdentity();
        $metricAssignmentReport = $this->metricAssignmentRepository
                ->aMetricAssignmentBelongsToUser($userId, $metricAssignmentId)
                ->submitReport($id, $observeTime, $metricAssignmentReportData);
        $this->metricAssignmentReportRepository->add($metricAssignmentReport);
        return $id;
    }

}
