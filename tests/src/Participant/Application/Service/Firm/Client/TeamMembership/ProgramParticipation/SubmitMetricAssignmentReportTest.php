<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation;

use DateTimeImmutable;
use Participant\ {
    Application\Service\Firm\Client\TeamMembership\TeamProgramParticipationRepository,
    Application\Service\Firm\Client\TeamMembershipRepository,
    Application\Service\Participant\MetricAssignment\MetricAssignmentReportRepository,
    Domain\DependencyModel\Firm\Client\TeamMembership,
    Domain\Model\TeamProgramParticipation,
    Domain\Service\MetricAssignmentReportDataProvider
};
use Tests\TestBase;

class SubmitMetricAssignmentReportTest extends TestBase
{

    protected $metricAssignmentReportRepository, $nextId = "nextId";
    protected $teamMembershipRepository, $teamMembership;
    protected $teamProgramParticipationRepository, $teamProgramParticipation;
    protected $service;
    protected $firmId = "firmId", $teamId = "teamId", $clientId = "clientId",
            $teamProgramParticipationId = "teamProgramParticipationId", $observationTime, $metricAssignmentReportDataProvider;

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

        $this->teamProgramParticipation = $this->buildMockOfClass(TeamProgramParticipation::class);
        $this->teamProgramParticipationRepository = $this->buildMockOfInterface(TeamProgramParticipationRepository::class);
        $this->teamProgramParticipationRepository->expects($this->any())
                ->method("ofId")
                ->with($this->teamProgramParticipationId)
                ->willReturn($this->teamProgramParticipation);

        $this->service = new SubmitMetricAssignmentReport(
                $this->metricAssignmentReportRepository, $this->teamMembershipRepository,
                $this->teamProgramParticipationRepository);

        $this->observationTime = new DateTimeImmutable();
        $this->metricAssignmentReportDataProvider = $this->buildMockOfClass(MetricAssignmentReportDataProvider::class);
    }

    protected function execute()
    {
        return $this->service->execute(
                        $this->firmId, $this->teamId, $this->clientId, $this->teamProgramParticipationId,
                        $this->observationTime, $this->metricAssignmentReportDataProvider);
    }

    public function test_execute_addMetricAssignmentReportToRepository()
    {
        $this->teamMembership->expects($this->once())
                ->method("submitMetricAssignmentReport")
                ->with($this->teamProgramParticipation, $this->nextId, $this->observationTime,
                        $this->metricAssignmentReportDataProvider);
        $this->metricAssignmentReportRepository->expects($this->once())
                ->method("add");
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }

}
