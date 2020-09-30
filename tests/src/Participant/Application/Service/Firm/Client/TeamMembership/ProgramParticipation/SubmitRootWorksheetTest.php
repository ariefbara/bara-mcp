<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation;

use Participant\ {
    Application\Service\Firm\Client\TeamMembership\TeamProgramParticipationRepository,
    Application\Service\Firm\Client\TeamMembershipRepository,
    Application\Service\Firm\Program\MissionRepository,
    Application\Service\Participant\WorksheetRepository,
    Domain\DependencyModel\Firm\Client\TeamMembership,
    Domain\DependencyModel\Firm\Program\Mission,
    Domain\Model\TeamProgramParticipation
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class SubmitRootWorksheetTest extends TestBase
{

    protected $service;
    protected $worksheetRepository, $nextId = 'nextId';
    protected $teamMembershipRepository, $teamMembership;
    protected $teamProgramParticipationRepository, $teamProgramParticipation;
    protected $missionRepository, $mission;
    protected $firmId = "firmId", $clientId = "clientId", $teamMembershipId = "teamMembershipId",
            $programParticipationId = "programParticipationId", $missionId = "missionId";
    protected $name = "worksheet name", $formRecordData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->worksheetRepository = $this->buildMockOfInterface(WorksheetRepository::class);
        $this->worksheetRepository->expects($this->any())->method("nextIdentity")->willReturn($this->nextId);

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

        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->missionRepository = $this->buildMockOfInterface(MissionRepository::class);
        $this->missionRepository->expects($this->any())
                ->method("ofId")
                ->with($this->missionId)
                ->willReturn($this->mission);

        $this->service = new SubmitRootWorksheet(
                $this->worksheetRepository, $this->teamMembershipRepository, $this->teamProgramParticipationRepository,
                $this->missionRepository);

        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }

    protected function execute()
    {
        return $this->service->execute(
                        $this->firmId, $this->clientId, $this->teamMembershipId, $this->programParticipationId,
                        $this->missionId, $this->name, $this->formRecordData);
    }
    public function test_execute_addWorksheetToRepository()
    {
        $this->teamMembership->expects($this->once())
                ->method("submitRootWorksheet")
                ->with($this->teamProgramParticipation, $this->nextId, $this->name, $this->mission, $this->formRecordData);
        $this->worksheetRepository->expects($this->once())
                ->method("add");
        $this->execute();
    }
    public function test_execute_returNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }

}
