<?php

namespace Participant\Application\Service\UserParticipant\MetricAssignment;

use Participant\Domain\Model\Participant\MetricAssignment\ {
    MetricAssignmentReport,
    MetricAssignmentReportData
};
use Tests\TestBase;

class UpdateMetricAssignmentReportTest extends TestBase
{
    protected $metricAssignmentReportRepository, $metricAssignmentReport;
    protected $service;
    protected $userId = "userId", $metricAssignmentReportId = "metricAssignmentReportId", $metricAssignmentReportData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->metricAssignmentReport = $this->buildMockOfClass(MetricAssignmentReport::class);
        $this->metricAssignmentReportRepository = $this->buildMockOfInterface(MetricAssignmentReportRepository::class);
        $this->metricAssignmentReportRepository->expects($this->any())
                ->method("aMetricAssignmentReportBelongsToUser")
                ->with($this->userId, $this->metricAssignmentReportId)
                ->willReturn($this->metricAssignmentReport);

        $this->service = new UpdateMetricAssignmentReport($this->metricAssignmentReportRepository);

        $this->metricAssignmentReportData = $this->buildMockOfClass(MetricAssignmentReportData::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->userId, $this->metricAssignmentReportId, $this->metricAssignmentReportData);
    }
    public function test_execute_updateReport()
    {
        $this->metricAssignmentReport->expects($this->once())
                ->method('update')
                ->with($this->metricAssignmentReportData);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->metricAssignmentReportRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
