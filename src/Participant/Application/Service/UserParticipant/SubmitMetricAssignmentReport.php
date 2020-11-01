<?php

namespace Participant\Application\Service\UserParticipant;

use DateTimeImmutable;
use Participant\{
    Application\Service\UserParticipantRepository,
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
     * @var UserParticipantRepository
     */
    protected $userParticipantRepository;

    function __construct(
            MetricAssignmentReportRepository $metricAssignmentReportRepository,
            UserParticipantRepository $userParticipantRepository)
    {
        $this->metricAssignmentReportRepository = $metricAssignmentReportRepository;
        $this->userParticipantRepository = $userParticipantRepository;
    }

    public function execute(
            string $userId, string $programParticipationId, DateTimeImmutable $observationTime,
            MetricAssignmentReportDataProvider $metricAssignmentReportDataProvider): string
    {
        $id = $this->metricAssignmentReportRepository->nextIdentity();
        $metricAssignmentReport = $this->userParticipantRepository->ofId($userId, $programParticipationId)
                ->submitMetricAssignmentReport($id, $observationTime, $metricAssignmentReportDataProvider);
        $this->metricAssignmentReportRepository->add($metricAssignmentReport);
        return $id;
    }

}
