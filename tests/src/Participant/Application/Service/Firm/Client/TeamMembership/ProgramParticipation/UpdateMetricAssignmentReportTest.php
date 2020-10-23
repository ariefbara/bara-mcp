<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation;

use Participant\{
    Application\Service\Firm\Client\TeamMembershipRepository,
    Application\Service\Participant\MetricAssignment\MetricAssignmentReportRepository,
    Domain\DependencyModel\Firm\Client\TeamMembership,
    Domain\Model\Participant\MetricAssignment\MetricAssignmentReport,
    Domain\Model\Participant\MetricAssignment\MetricAssignmentReportData
};
use Tests\TestBase;

class UpdateMetricAssignmentReportTest extends TestBase
{

    protected $metricAssignmentReportRepository, $metricAssignmentReport;
    protected $teamMembershipRepository, $teamMembership;
    protected $service;
    protected $firmId = "firmId", $teamId = "teamId", $clientId = "clientId",
            $metricAssignmentReportId = "metricAssignmentReportId", $metricAssignmentReportData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->metricAssignmentReport = $this->buildMockOfClass(MetricAssignmentReport::class);
        $this->metricAssignmentReportRepository = $this->buildMockOfInterface(MetricAssignmentReportRepository::class);
        $this->metricAssignmentReportRepository->expects($this->any())
                ->method("ofId")
                ->with($this->metricAssignmentReportId)
                ->willReturn($this->metricAssignmentReport);

        $this->teamMembership = $this->buildMockOfClass(TeamMembership::class);
        $this->teamMembershipRepository = $this->buildMockOfInterface(TeamMembershipRepository::class);
        $this->teamMembershipRepository->expects($this->any())
                ->method("aTeamMembershipCorrespondWithTeam")
                ->with($this->firmId, $this->clientId, $this->teamId)
                ->willReturn($this->teamMembership);


        $this->service = new UpdateMetricAssignmentReport(
                $this->metricAssignmentReportRepository, $this->teamMembershipRepository);

        $this->metricAssignmentReportData = $this->buildMockOfClass(MetricAssignmentReportData::class);
    }

    protected function execute()
    {
        $this->service->execute(
                $this->firmId, $this->teamId, $this->clientId, $this->metricAssignmentReportId,
                $this->metricAssignmentReportData);
    }
    public function test_execute_executeTeamMembershipUpdateMetricAssignmentReport()
    {
        $this->teamMembership->expects($this->once())
                ->method("updateMetricAssignmentReport")
                ->with($this->metricAssignmentReport, $this->metricAssignmentReportData);
        $this->execute();
    }
    public function test_execute_updateMetricAssignmentReportRepository()
    {
        $this->metricAssignmentReportRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }

}
