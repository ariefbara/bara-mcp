<?php

namespace Participant\Application\Service\ClientParticipant;

use Participant\ {
    Application\Service\ClientParticipantRepository,
    Application\Service\Firm\Program\MissionRepository,
    Application\Service\Participant\WorksheetRepository,
    Domain\DependencyModel\Firm\Program\Mission,
    Domain\Model\ClientParticipant,
    Domain\Model\Participant\Worksheet
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;


class WorksheetAddBranchTest extends TestBase
{

    protected $service;
    protected $worksheetRepository, $worksheet, $nextId = 'nextId';
    protected $clientParticipantRepository, $clientParticipant;
    protected $missionRepository, $mission;
    protected $firmId = 'firmId', $clientId = 'clientId', $programParticipationId = 'programParticipationId', $worksheetId = 'worksheetId', $missionId = 'missionId';
    protected $name = 'new worksheet name', $formRecordData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->worksheet = $this->buildMockOfClass(Worksheet::class);
        $this->worksheetRepository = $this->buildMockOfInterface(WorksheetRepository::class);
        $this->worksheetRepository->expects($this->any())
                ->method('ofId')
                ->with($this->worksheetId)
                ->willReturn($this->worksheet);
        $this->worksheetRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->nextId);
        
        $this->clientParticipant = $this->buildMockOfClass(ClientParticipant::class);
        $this->clientParticipantRepository = $this->buildMockOfInterface(ClientParticipantRepository::class);
        $this->clientParticipantRepository->expects($this->any())
                ->method("ofId")
                ->with($this->firmId, $this->clientId, $this->programParticipationId)
                ->willReturn($this->clientParticipant);

        $this->missionRepository = $this->buildMockOfInterface(MissionRepository::class);
        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->missionRepository->expects($this->any())
                ->method('ofId')
                ->with($this->missionId)
                ->willReturn($this->mission);

        $this->service = new WorksheetAddBranch(
                $this->worksheetRepository, $this->clientParticipantRepository, $this->missionRepository);

        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }

    protected function execute()
    {
        return $this->service->execute(
                        $this->firmId, $this->clientId, $this->programParticipationId, $this->worksheetId, $this->missionId,
                        $this->name, $this->formRecordData);
    }

    public function test_execute_addBranchWorksheetToRepository()
    {
        $branchWorksheet = $this->buildMockOfClass(Worksheet::class);
        $this->clientParticipant->expects($this->once())
                ->method("submitBranchWorksheet")
                ->with($this->worksheet, $this->nextId, $this->name, $this->mission, $this->formRecordData)
                ->willReturn($branchWorksheet);
        $this->worksheetRepository->expects($this->once())
                ->method('add')
                ->with($branchWorksheet);
        $this->execute();
    }

    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }

}
