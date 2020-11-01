<?php

namespace Participant\Application\Service\ClientParticipant;

use Participant\ {
    Application\Service\ClientParticipantRepository,
    Domain\Model\ClientParticipant,
    Domain\Model\Participant\MetricAssignment\MetricAssignmentReport,
    Domain\Service\MetricAssignmentReportDataProvider
};
use Tests\TestBase;

class UpdateMetricAssignmentReportTest extends TestBase
{

    protected $metricAssignmentReportRepository, $metricAssignmentReport;
    protected $service;
    protected $firmId = "firmId", $clientId = "clientId", $metricAssignmentReportId = "metricAssignmentReportId";
    protected $metricAssignmentReportDataProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->metricAssignmentReport = $this->buildMockOfClass(MetricAssignmentReport::class);
        $this->metricAssignmentReportRepository = $this->buildMockOfInterface(MetricAssignmentReportRepository::class);
        $this->metricAssignmentReportRepository->expects($this->any())
                ->method("aMetricAssignmentReportBelongsToClient")
                ->with($this->firmId, $this->clientId, $this->metricAssignmentReportId)
                ->willReturn($this->metricAssignmentReport);

        $this->service = new UpdateMetricAssignmentReport($this->metricAssignmentReportRepository);

        $this->metricAssignmentReportDataProvider = $this->buildMockOfClass(MetricAssignmentReportDataProvider::class);
    }

    protected function execute()
    {
        $this->service->execute(
                $this->firmId, $this->clientId, $this->metricAssignmentReportId, $this->metricAssignmentReportDataProvider);
    }

    public function test_execute_updateReport()
    {
        $this->metricAssignmentReport->expects($this->once())
                ->method("update");
        $this->execute();
    }

    public function test_execute_updateRepository()
    {
        $this->metricAssignmentReportRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }

}
