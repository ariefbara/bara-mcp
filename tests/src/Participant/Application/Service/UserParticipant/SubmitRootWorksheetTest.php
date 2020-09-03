<?php

namespace Participant\Application\Service\UserParticipant;

use Participant\ {
    Application\Service\ClientParticipant\WorksheetAddRoot,
    Application\Service\Participant\WorksheetRepository,
    Application\Service\UserParticipantRepository,
    Domain\Model\DependencyEntity\Firm\Program\Mission,
    Domain\Model\UserParticipant
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class SubmitRootWorksheetTest extends TestBase
{
    protected $service;
    protected $worksheetRepository, $nextId = 'nextId';
    protected $userParticipantRepository, $userParticipant;
    protected $missionRepository, $mission;
    
    protected $userId = 'userId', $userParticipantId = 'userParticipantId', $missionId = 'missionId';
    protected $name = "new worksheet name", $formRecordData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->worksheetRepository = $this->buildMockOfInterface(WorksheetRepository::class);
        $this->worksheetRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->nextId);
        
        $this->userParticipant = $this->buildMockOfClass(UserParticipant::class);
        $this->userParticipantRepository = $this->buildMockOfInterface(UserParticipantRepository::class);
        $this->userParticipantRepository->expects($this->any())
                ->method('ofId')
                ->with($this->userId, $this->userParticipantId)
                ->willReturn($this->userParticipant);
        
        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->mission->expects($this->any())
                ->method('isRootMission')
                ->willReturn(true);
        $this->missionRepository = $this->buildMockOfInterface(MissionRepository::class);
        $this->missionRepository->expects($this->any())
                ->method('aMissionInProgramWhereUserParticipate')
                ->with($this->userId, $this->userParticipantId, $this->missionId)
                ->willReturn($this->mission);
        
        $this->service = new SubmitRootWorksheet(
                $this->worksheetRepository, $this->userParticipantRepository, $this->missionRepository);
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    protected function execute()
    {
        $this->mission->expects($this->any())
                ->method('createWorksheetFormRecord')
                ->with($this->nextId, $this->formRecordData);
        return $this->service->execute($this->userId, $this->userParticipantId, $this->missionId, $this->name, $this->formRecordData);
    }
    
    public function test_addWorksheetToRepository()
    {
        $this->userParticipant->expects($this->once())
                ->method('createRootWorksheet')
                ->with($this->nextId, $this->name, $this->mission, $this->anything());
        $this->worksheetRepository->expects($this->once())
                ->method('add');
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }
}
