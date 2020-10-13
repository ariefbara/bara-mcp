<?php

namespace Participant\Application\Service\UserParticipant;

use Participant\ {
    Application\Service\Participant\WorksheetRepository,
    Application\Service\UserParticipantRepository,
    Domain\DependencyModel\Firm\Program\Mission,
    Domain\Model\Participant\Worksheet,
    Domain\Model\UserParticipant
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class SubmitBranchWorksheetTest extends TestBase
{
    protected $service;
    protected $worksheetRepository, $worksheet, $nextId = 'nextId';
    protected $userParticipantRepository, $userParticipant;
    protected $missionRepository, $mission;
    protected $userId = 'userId', $userParticipantId = 'userParticipantId', $worksheetId = 'worksheetId', $missionId = 'missionId';
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
        
        $this->userParticipant = $this->buildMockOfClass(UserParticipant::class);
        $this->userParticipantRepository = $this->buildMockOfInterface(UserParticipantRepository::class);
        $this->userParticipantRepository->expects($this->any())
                ->method("ofId")
                ->with($this->userId, $this->userParticipantId)
                ->willReturn($this->userParticipant);

        $this->missionRepository = $this->buildMockOfInterface(MissionRepository::class);
        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->missionRepository->expects($this->any())
                ->method('ofId')
                ->with($this->missionId)
                ->willReturn($this->mission);

        $this->service = new SubmitBranchWorksheet(
                $this->worksheetRepository, $this->userParticipantRepository, $this->missionRepository);

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
        $this->userParticipant->expects($this->once())
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
