<?php

namespace Participant\Application\Service\UserParticipant;

use Participant\Domain\Service\MetricAssignmentReportDataProvider;

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
            string $userId, string $metricAssignmentReportId,
            MetricAssignmentReportDataProvider $metricAssignmentReportDataProvider): void
    {
        $this->metricAssignmentReportRepository
                ->aMetricAssignmentReportBelongsToUser($userId, $metricAssignmentReportId)
                ->update($metricAssignmentReportDataProvider);
        $this->metricAssignmentReportRepository->update();
    }

}
