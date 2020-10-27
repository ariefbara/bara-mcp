<?php

namespace Participant\Application\Service\UserParticipant\MetricAssignment;

use Participant\Domain\Model\Participant\MetricAssignment\MetricAssignmentReportData;

class UpdateMetricAssignmentReport
{

    /**
     *
     * @var MetricAssignmentReportRepository
     */
    protected $metricAssignmentReportRepository;

    public function __construct(MetricAssignmentReportRepository $metricAssignmentReportRepository)
    {
        $this->metricAssignmentReportRepository = $metricAssignmentReportRepository;
    }

    public function execute(
            string $userId, string $metricAssignmentReportId, MetricAssignmentReportData $metricAssignmentReportData): void
    {
        $this->metricAssignmentReportRepository
                ->aMetricAssignmentReportBelongsToUser($userId, $metricAssignmentReportId)
                ->update($metricAssignmentReportData);
        $this->metricAssignmentReportRepository->update();
    }

}
