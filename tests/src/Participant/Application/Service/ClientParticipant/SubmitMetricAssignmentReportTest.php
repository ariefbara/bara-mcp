<?php

namespace Participant\Application\Service\ClientParticipant;

use DateTimeImmutable;
use Participant\{
    Application\Service\ClientParticipantRepository,
    Domain\Model\ClientParticipant,
    Domain\Service\MetricAssignmentReportDataProvider
};
use Tests\TestBase;

class SubmitMetricAssignmentReportTest extends TestBase
{

    protected $metricAssignmentReportRepository, $nextId = "nextId";
    protected $clientParticipantRepository, $clientParticipant;
    protected $service;
    protected $firmId = "firmId", $clientId = "clientId", $clientProgramParticipationId = "clientProgramParticipationId";
    protected $observationTime, $metricAssignmentReportDataProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->metricAssignmentReportRepository = $this->buildMockOfInterface(MetricAssignmentReportRepository::class);
        $this->metricAssignmentReportRepository->expects($this->any())
                ->method("nextIdentity")
                ->willReturn($this->nextId);

        $this->clientParticipant = $this->buildMockOfClass(ClientParticipant::class);
        $this->clientParticipantRepository = $this->buildMockOfInterface(ClientParticipantRepository::class);
        $this->clientParticipantRepository->expects($this->any())
                ->method("ofId")
                ->with($this->firmId, $this->clientId, $this->clientProgramParticipationId)
                ->willReturn($this->clientParticipant);

        $this->service = new SubmitMetricAssignmentReport(
                $this->metricAssignmentReportRepository, $this->clientParticipantRepository);

        $this->observationTime = new DateTimeImmutable();
        $this->metricAssignmentReportDataProvider = $this->buildMockOfClass(MetricAssignmentReportDataProvider::class);
    }

    protected function execute()
    {
        return $this->service->execute(
                        $this->firmId, $this->clientId, $this->clientProgramParticipationId, $this->observationTime,
                        $this->metricAssignmentReportDataProvider);
    }

    public function test_execute_addReportToRepository()
    {
        $this->clientParticipant->expects($this->once())
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
