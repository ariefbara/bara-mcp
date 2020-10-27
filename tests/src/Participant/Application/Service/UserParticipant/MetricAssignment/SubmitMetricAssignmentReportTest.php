<?php

namespace Participant\Application\Service\UserParticipant\MetricAssignment;

use DateTimeImmutable;
use Participant\ {
    Application\Service\UserParticipant\MetricAssignmentRepository,
    Domain\Model\Participant\MetricAssignment,
    Domain\Model\Participant\MetricAssignment\MetricAssignmentReportData
};
use Tests\TestBase;

class SubmitMetricAssignmentReportTest extends TestBase
{
    protected $metricAssignmentReportRepository, $nextId = "nextId";
    protected $metricAssignmentRepository, $metricAssignment;
    protected $service;
    protected $userId = "userId", $metricAssignmentId = "metricAssignmentId", $observeTime, $metricAssignmentReportData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->metricAssignmentReportRepository = $this->buildMockOfInterface(MetricAssignmentReportRepository::class);
        $this->metricAssignmentReportRepository->expects($this->any())
                ->method("nextIdentity")
                ->willReturn($this->nextId);

        $this->metricAssignment = $this->buildMockOfClass(MetricAssignment::class);
        $this->metricAssignmentRepository = $this->buildMockOfInterface(MetricAssignmentRepository::class);
        $this->metricAssignmentRepository->expects($this->any())
                ->method("aMetricAssignmentBelongsToUser")
                ->with($this->userId, $this->metricAssignmentId)
                ->willReturn($this->metricAssignment);

        $this->service = new SubmitMetricAssignmentReport(
                $this->metricAssignmentReportRepository, $this->metricAssignmentRepository);

        $this->observeTime = new DateTimeImmutable();
        $this->metricAssignmentReportData = $this->buildMockOfClass(MetricAssignmentReportData::class);
    }
    
    protected function execute()
    {
        return $this->service->execute(
                $this->userId, $this->metricAssignmentId, $this->observeTime, $this->metricAssignmentReportData);
    }
    
    public function test_execute_addReportToRepository()
    {
        $this->metricAssignment->expects($this->once())
                ->method("submitReport")
                ->with($this->nextId, $this->observeTime, $this->metricAssignmentReportData);
        $this->metricAssignmentReportRepository->expects($this->once())
                ->method("add");
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }
}
