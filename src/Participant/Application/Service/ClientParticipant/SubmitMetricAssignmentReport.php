<?php

namespace Participant\Application\Service\ClientParticipant;

use DateTimeImmutable;
use Participant\{
    Application\Service\ClientParticipantRepository,
    Domain\Service\MetricAssignmentReportDataProvider
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
     * @var ClientParticipantRepository
     */
    protected $clientParticipantRepository;

    function __construct(
            MetricAssignmentReportRepository $metricAssignmentReportRepository,
            ClientParticipantRepository $clientParticipantRepository)
    {
        $this->metricAssignmentReportRepository = $metricAssignmentReportRepository;
        $this->clientParticipantRepository = $clientParticipantRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $clientProgramParticipationId, ?DateTimeImmutable $observationTime,
            MetricAssignmentReportDataProvider $metricAssignmentReportDataProvider): string
    {
        $id = $this->metricAssignmentReportRepository->nextIdentity();
        $metricAssignmentReport = $this->clientParticipantRepository
                ->ofId($firmId, $clientId, $clientProgramParticipationId)
                ->submitMetricAssignmentReport($id, $observationTime, $metricAssignmentReportDataProvider);
        $this->metricAssignmentReportRepository->add($metricAssignmentReport);
        return $id;
    }

}
