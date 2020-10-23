<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation;

use Participant\{
    Application\Service\Firm\Client\TeamMembershipRepository,
    Application\Service\Participant\MetricAssignment\MetricAssignmentReportRepository,
    Application\Service\Participant\MetricAssignmentRepository,
    Domain\DependencyModel\Firm\Client\TeamMembership,
    Domain\Model\Participant\MetricAssignment,
    Domain\Model\Participant\MetricAssignment\MetricAssignmentReportData
};
use Tests\TestBase;

class SubmitMetricAssignmentReportTest extends TestBase
{

    protected $metricAssignmentReportRepository, $nextId = "nextId";
    protected $teamMembershipRepository, $teamMembership;
    protected $metricAssignmentRepository, $metricAssignment;
    protected $service;
    protected $firmId = "firmId", $teamId = "teamId", $clientId = "clientId",
            $metricAssignmentId = "metricAssignmentId", $observeTime, $metricAssignmentReportData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->metricAssignmentReportRepository = $this->buildMockOfInterface(MetricAssignmentReportRepository::class);
        $this->metricAssignmentReportRepository->expects($this->any())
                ->method("nextIdentity")
                ->willReturn($this->nextId);

        $this->teamMembership = $this->buildMockOfClass(TeamMembership::class);
        $this->teamMembershipRepository = $this->buildMockOfInterface(TeamMembershipRepository::class);
        $this->teamMembershipRepository->expects($this->any())
                ->method("aTeamMembershipCorrespondWithTeam")
                ->with($this->firmId, $this->clientId, $this->teamId)
                ->willReturn($this->teamMembership);

        $this->metricAssignment = $this->buildMockOfClass(MetricAssignment::class);
        $this->metricAssignmentRepository = $this->buildMockOfInterface(MetricAssignmentRepository::class);
        $this->metricAssignmentRepository->expects($this->any())
                ->method("ofId")
                ->with($this->metricAssignmentId)
                ->willReturn($this->metricAssignment);

        $this->service = new SubmitMetricAssignmentReport(
                $this->metricAssignmentReportRepository, $this->teamMembershipRepository,
                $this->metricAssignmentRepository);

        $this->observeTime = new \DateTimeImmutable();
        $this->metricAssignmentReportData = $this->buildMockOfClass(MetricAssignmentReportData::class);
    }

    protected function execute()
    {
        return $this->service->execute(
                        $this->firmId, $this->teamId, $this->clientId, $this->metricAssignmentId, $this->observeTime,
                        $this->metricAssignmentReportData);
    }

    public function test_execute_addMetricAssignmentReportToRepository()
    {
        $this->teamMembership->expects($this->once())
                ->method("submitReportInMetricAssignment")
                ->with($this->metricAssignment, $this->nextId, $this->observeTime, $this->metricAssignmentReportData);
        $this->metricAssignmentReportRepository->expects($this->once())
                ->method("add");
        $this->execute();
    }

    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }

}
