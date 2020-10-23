<?php

namespace Firm\Application\Service\Firm\Program\Participant;

use Firm\{
    Application\Service\Firm\Program\CoordinatorRepository,
    Application\Service\Firm\Program\ParticipantRepository,
    Domain\Model\Firm\Program\Coordinator,
    Domain\Model\Firm\Program\Participant,
    Domain\Service\MetricAssignmentDataProvider
};
use Tests\TestBase;

class AssignMetricsTest extends TestBase
{

    protected $participantRepository, $participant;
    protected $coordinatorRepository, $coordinator;
    protected $service;
    protected $programId = "programId", $personnelId = "personnelId", $participantId = "participantId";
    protected $metricAssignmentDataCollector;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->participantRepository = $this->buildMockOfInterface(ParticipantRepository::class);
        $this->participantRepository->expects($this->any())
                ->method("ofId")
                ->with($this->participantId)
                ->willReturn($this->participant);

        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->coordinatorRepository = $this->buildMockOfInterface(CoordinatorRepository::class);
        $this->coordinatorRepository->expects($this->any())
                ->method("aCoordinatorCorrespondWithPersonnel")
                ->with($this->programId, $this->personnelId)
                ->willReturn($this->coordinator);

        $this->service = new AssignMetrics($this->participantRepository, $this->coordinatorRepository);

        $this->metricAssignmentDataCollector = $this->buildMockOfClass(MetricAssignmentDataProvider::class);
    }

    protected function execute()
    {
        $this->service->execute(
                $this->programId, $this->personnelId, $this->participantId, $this->metricAssignmentDataCollector);
    }
    public function test_execute_coordinatorAssignMetricToParticipant()
    {
        $this->coordinator->expects($this->once())
                ->method("assignMetricsToParticipant")
                ->with($this->participant, $this->metricAssignmentDataCollector);
        $this->execute();
    }
    public function test_execute_updateParticipantRepository()
    {
        $this->participantRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }

}
