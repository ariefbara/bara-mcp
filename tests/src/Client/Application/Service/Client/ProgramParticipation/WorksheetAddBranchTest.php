<?php

namespace Client\Application\Service\Client\ProgramParticipation;

use Client\{
    Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId,
    Application\Service\Client\ProgramParticipation\WorksheetAddBranch,
    Application\Service\Client\ProgramParticipation\WorksheetRepository,
    Application\Service\Firm\Program\MissionRepository,
    Domain\Model\Client\ProgramParticipation\Worksheet,
    Domain\Model\Firm\Program\Mission
};
use Shared\Domain\Model\{
    FormRecord,
    FormRecordData
};
use Tests\TestBase;

class WorksheetAddBranchTest extends TestBase
{

    protected $service;
    protected $programParticipationCompositionId, $clientId = 'clientId', $programParticipationId = 'programParticipationId';
    protected $worksheetRepository, $parentWorksheet, $parentWorksheetId = 'parentWorksheetId', $nextId = 'nextId';
    protected $missionRepository, $mission, $missionId = 'missionId';
    protected $formRecordData, $name = 'new worksheet name';
    protected $formRecord;

    protected function setUp(): void
    {
        parent::setUp();
        $this->programParticipationCompositionId = new ProgramParticipationCompositionId($this->clientId,
                $this->programParticipationId);
        $this->worksheetRepository = $this->buildMockOfInterface(WorksheetRepository::class);
        $this->parentWorksheet = $this->buildMockOfClass(Worksheet::class);
        $this->worksheetRepository->expects($this->any())
                ->method('ofId')
                ->with($this->programParticipationCompositionId, $this->parentWorksheetId)
                ->willReturn($this->parentWorksheet);
        $this->worksheetRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->nextId);

        $this->missionRepository = $this->buildMockOfInterface(MissionRepository::class);
        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->missionRepository->expects($this->any())
                ->method('aMissionInProgramWhereClientParticipate')
                ->with($this->clientId, $this->programParticipationId, $this->missionId)
                ->willReturn($this->mission);

        $this->service = new WorksheetAddBranch($this->worksheetRepository, $this->missionRepository);

        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
    }

    protected function execute()
    {
        return $this->service->execute(
                        $this->programParticipationCompositionId, $this->parentWorksheetId, $this->missionId,
                        $this->name, $this->formRecordData);
    }

    public function test_execute_addBranchWorksheetToRepository()
    {
        $branchWorksheet = $this->buildMockOfClass(Worksheet::class);
        $this->mission->expects($this->once())
                ->method('createWorksheetFormRecord')
                ->with($this->nextId, $this->formRecordData)
                ->willReturn($this->formRecord);
        $this->parentWorksheet->expects($this->once())
                ->method('createBranchWorksheet')
                ->with($this->nextId, $this->name, $this->mission, $this->formRecord)
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
