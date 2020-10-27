<?php

namespace Participant\Application\Service\ClientParticipant\MetricAssignment;

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
            string $clientId, string $metricAssignmentReportId, MetricAssignmentReportData $metricAssignmentReportData): void
    {
        $this->metricAssignmentReportRepository
                ->aMetricAssignmentReportBelongsToClient($clientId, $metricAssignmentReportId)
                ->update($metricAssignmentReportData);
        $this->metricAssignmentReportRepository->update();
    }

}
