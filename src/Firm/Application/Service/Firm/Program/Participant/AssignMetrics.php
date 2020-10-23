<?php

namespace Firm\Application\Service\Firm\Program\Participant;

use Firm\{
    Application\Service\Firm\Program\CoordinatorRepository,
    Application\Service\Firm\Program\ParticipantRepository,
    Domain\Service\MetricAssignmentDataProvider
};

class AssignMetrics
{

    /**
     *
     * @var ParticipantRepository
     */
    protected $participantRepository;

    /**
     *
     * @var CoordinatorRepository
     */
    protected $coordinatorRepository;

    public function __construct(
            ParticipantRepository $participantRepository, CoordinatorRepository $coordinatorRepository)
    {
        $this->participantRepository = $participantRepository;
        $this->coordinatorRepository = $coordinatorRepository;
    }

    public function execute(
            string $programId, string $personnelId, string $participantId,
            MetricAssignmentDataProvider $metricAssignmentDataProvider)
    {
        $participant = $this->participantRepository->ofId($participantId);
        $this->coordinatorRepository->aCoordinatorCorrespondWithPersonnel($programId, $personnelId)
                ->assignMetricsToParticipant($participant, $metricAssignmentDataProvider);
        $this->participantRepository->update();
    }

}
