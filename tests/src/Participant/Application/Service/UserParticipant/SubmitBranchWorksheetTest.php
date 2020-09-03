<?php

namespace Participant\Application\Service\UserParticipant;

use Participant\ {
    Application\Service\Participant\WorksheetRepository,
    Domain\Model\DependencyEntity\Firm\Program\Mission,
    Domain\Model\Participant\Worksheet
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class SubmitBranchWorksheetTest extends TestBase
{
    protected $service;
    protected $worksheetRepository, $worksheet, $nextId = 'nextId';
    protected $missionRepository, $mission;
    protected $userId = 'userId', $userParticipantId = 'userParticipantId', $worksheetId = 'worksheetId', $missionId = 'missionId';
    protected $name = 'new worksheet name', $formRecordData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->worksheet = $this->buildMockOfClass(Worksheet::class);
        $this->worksheetRepository = $this->buildMockOfInterface(WorksheetRepository::class);
        $this->worksheetRepository->expects($this->any())
                ->method('aWorksheetBelongsToUserParticipant')
                ->with($this->userId, $this->userParticipantId, $this->worksheetId)
                ->willReturn($this->worksheet);
        $this->worksheetRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->nextId);

        $this->missionRepository = $this->buildMockOfInterface(MissionRepository::class);
        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->missionRepository->expects($this->any())
                ->method('aMissionInProgramWhereUserParticipate')
                ->with($this->userId, $this->userParticipantId, $this->missionId)
                ->willReturn($this->mission);

        $this->service = new SubmitBranchWorksheet($this->worksheetRepository, $this->missionRepository);

        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }

    protected function execute()
    {
        return $this->service->execute(
                        $this->userId, $this->userParticipantId, $this->worksheetId, $this->missionId,
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
