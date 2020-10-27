<?php

namespace Participant\Application\Service\ClientParticipant\MetricAssignment;

use DateTimeImmutable;
use Participant\{
    Application\Service\ClientParticipant\MetricAssignmentRepository,
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
            string $clientId, string $metricAssignmentId, DateTimeImmutable $observeTime,
            MetricAssignmentReportData $metricAssignmentReportData): string
    {
        $id = $this->metricAssignmentReportRepository->nextIdentity();
        $metricAssignmentReport = $this->metricAssignmentRepository
                ->aMetricAssignmentBelongsToClient($clientId, $metricAssignmentId)
                ->submitReport($id, $observeTime, $metricAssignmentReportData);
        $this->metricAssignmentReportRepository->add($metricAssignmentReport);
        return $id;
    }

}
