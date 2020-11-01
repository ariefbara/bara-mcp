<?php

namespace Participant\Application\Service\UserParticipant;

use DateTimeImmutable;
use Participant\ {
    Application\Service\UserParticipantRepository,
    Domain\Model\UserParticipant,
    Domain\Service\MetricAssignmentReportDataProvider
};
use Tests\TestBase;

class SubmitMetricAssignmentReportTest extends TestBase
{
    protected $metricAssignmentReportRepository, $nextId = "nextId";
    protected $userParticipantRepository, $userParticipant;
    protected $service;
    protected $userId = "userId", $programParticipationId = "programParticipationId";
    protected $observationTime, $metricAssignmentReportDataProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->metricAssignmentReportRepository = $this->buildMockOfInterface(MetricAssignmentReportRepository::class);
        $this->metricAssignmentReportRepository->expects($this->any())
                ->method("nextIdentity")
                ->willReturn($this->nextId);

        $this->userParticipant = $this->buildMockOfClass(UserParticipant::class);
        $this->userParticipantRepository = $this->buildMockOfInterface(UserParticipantRepository::class);
        $this->userParticipantRepository->expects($this->any())
                ->method("ofId")
                ->with($this->userId, $this->programParticipationId)
                ->willReturn($this->userParticipant);

        $this->service = new SubmitMetricAssignmentReport(
                $this->metricAssignmentReportRepository, $this->userParticipantRepository);

        $this->observationTime = new DateTimeImmutable();
        $this->metricAssignmentReportDataProvider = $this->buildMockOfClass(MetricAssignmentReportDataProvider::class);
    }
    
    protected function execute()
    {
        return $this->service->execute(
                $this->userId, $this->programParticipationId, $this->observationTime, $this->metricAssignmentReportDataProvider);
    }
    
    public function test_execute_addReportToRepository()
    {
        $this->userParticipant->expects($this->once())
                ->method("submitMetricAssignmentReport")
                ->with($this->nextId, $this->observationTime, $this->metricAssignmentReportDataProvider);
        $this->metricAssignmentReportRepository->expects($this->once())
                ->method("add");
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }
}
