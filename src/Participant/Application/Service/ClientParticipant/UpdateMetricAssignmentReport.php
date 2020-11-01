<?php

namespace Participant\Application\Service\ClientParticipant;

use Participant\{
    Application\Service\ClientParticipantRepository,
    Domain\Service\MetricAssignmentReportDataProvider
};

class UpdateMetricAssignmentReport
{

    /**
     *
     * @var MetricAssignmentReportRepository
     */
    protected $metricAssignmentReportRepository;

    function __construct(MetricAssignmentReportRepository $metricAssignmentReportRepository)
    {
        $this->metricAssignmentReportRepository = $metricAssignmentReportRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $metricAssignmentReportId,
            MetricAssignmentReportDataProvider $metricAssignmentReportDataProvider): void
    {
        $this->metricAssignmentReportRepository
                ->aMetricAssignmentReportBelongsToClient($firmId, $clientId, $metricAssignmentReportId)
                ->update($metricAssignmentReportDataProvider);
        $this->metricAssignmentReportRepository->update();
    }

}
