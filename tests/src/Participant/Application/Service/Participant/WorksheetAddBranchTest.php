<?php

namespace Participant\Application\Service\Participant;

use Participant\Domain\Model\ {
    DependencyEntity\Firm\Program\Mission,
    Participant\Worksheet
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class WorksheetAddBranchTest extends TestBase
{

    protected $service;
    protected $worksheetRepository, $worksheet, $nextId = 'nextId';
    protected $missionRepository, $mission;
    protected $firmId = 'firmId', $clientId = 'clientId', $programId = 'programId', $worksheetId = 'worksheetId', $missionId = 'missionId';
    protected $name = 'new worksheet name', $formRecordData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->worksheet = $this->buildMockOfClass(Worksheet::class);
        $this->worksheetRepository = $this->buildMockOfInterface(WorksheetRepository::class);
        $this->worksheetRepository->expects($this->any())
                ->method('aWorksheetOfClientParticipant')
                ->with($this->firmId, $this->clientId, $this->programId, $this->worksheetId)
                ->willReturn($this->worksheet);
        $this->worksheetRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->nextId);

        $this->missionRepository = $this->buildMockOfInterface(MissionRepository::class);
        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->missionRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId, $this->programId, $this->missionId)
                ->willReturn($this->mission);

        $this->service = new WorksheetAddBranch($this->worksheetRepository, $this->missionRepository);

        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }

    protected function execute()
    {
        return $this->service->execute(
                        $this->firmId, $this->clientId, $this->programId, $this->worksheetId, $this->missionId,
                        $this->name, $this->formRecordData);
    }

    public function test_execute_addBranchWorksheetToRepository()
    {
        $branchWorksheet = $this->buildMockOfClass(Worksheet::class);
        $this->mission->expects($this->once())
                ->method('createWorksheetFormRecord')
                ->with($this->nextId, $this->formRecordData);
        $this->worksheet->expects($this->once())
                ->method('createBranchWorksheet')
                ->with($this->nextId, $this->name, $this->mission, $this->anything())
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
