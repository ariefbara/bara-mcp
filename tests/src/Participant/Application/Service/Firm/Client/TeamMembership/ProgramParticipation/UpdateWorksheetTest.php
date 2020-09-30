<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation;

use Participant\{
    Application\Service\Firm\Client\TeamMembership\TeamProgramParticipationRepository,
    Application\Service\Firm\Client\TeamMembershipRepository,
    Application\Service\Participant\WorksheetRepository,
    Domain\DependencyModel\Firm\Client\TeamMembership,
    Domain\Model\Participant\Worksheet,
    Domain\Model\TeamProgramParticipation
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class UpdateWorksheetTest extends TestBase
{

    protected $service;
    protected $worksheetRepository, $worksheet;
    protected $teamMembershipRepository, $teamMembership;
    protected $teamProgramParticipationRepository, $teamProgramParticipation;
    protected $firmId = "firmId", $clientId = "clientId", $teamMembershipId = "teamMembershipId",
            $programParticipationId = "programParticipationId";
    protected $worksheetId = "worksheetId", $name = "worksheet name", $formRecordData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->worksheetRepository = $this->buildMockOfInterface(WorksheetRepository::class);
        $this->worksheet = $this->buildMockOfClass(Worksheet::class);
        $this->worksheetRepository->expects($this->any())
                ->method("ofId")
                ->with($this->worksheetId)
                ->willReturn($this->worksheet);

        $this->teamMembership = $this->buildMockOfClass(TeamMembership::class);
        $this->teamMembershipRepository = $this->buildMockOfInterface(TeamMembershipRepository::class);
        $this->teamMembershipRepository->expects($this->any())
                ->method("ofId")
                ->with($this->firmId, $this->clientId, $this->teamMembershipId)
                ->willReturn($this->teamMembership);

        $this->teamProgramParticipation = $this->buildMockOfClass(TeamProgramParticipation::class);
        $this->teamProgramParticipationRepository = $this->buildMockOfInterface(TeamProgramParticipationRepository::class);
        $this->teamProgramParticipationRepository->expects($this->any())
                ->method("ofId")
                ->with($this->programParticipationId)
                ->willReturn($this->teamProgramParticipation);

        $this->service = new UpdateWorksheet(
                $this->worksheetRepository, $this->teamMembershipRepository, $this->teamProgramParticipationRepository);

        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    protected function execute()
    {
        $this->service->execute(
                $this->firmId, $this->clientId, $this->teamMembershipId, $this->programParticipationId, $this->worksheetId, $this->name, $this->formRecordData);
    }
    public function test_execute_udpateWorksheetByTeamMembership()
    {
        $this->teamMembership->expects($this->once())
                ->method("updateWorksheet")
                ->with($this->teamProgramParticipation, $this->worksheet, $this->name, $this->formRecordData);
        $this->execute();
    }
    public function test_execute_updateWorksheetRepository()
    {
        $this->worksheetRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }

}
