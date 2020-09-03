<?php

namespace Participant\Application\Service\ClientParticipant;

use Participant\ {
    Application\Service\ClientParticipant\WorksheetAddRoot,
    Application\Service\ClientParticipantRepository,
    Application\Service\Participant\WorksheetRepository,
    Domain\Model\ClientParticipant,
    Domain\Model\DependencyEntity\Firm\Program\Mission
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class WorksheetAddRootTest extends TestBase
{
    protected $service;
    protected $worksheetRepository, $nextId = 'nextId';
    protected $clientParticipantRepository, $clientParticipant, $clientParticipantId = "clientParticipantId";
    protected $missionRepository, $mission;
    
    protected $firmId = 'firmId', $clientId = 'clientId', $programParticipationId = 'programParticipationId', $missionId = 'missionId';
    protected $name = "new worksheet name", $formRecordData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->worksheetRepository = $this->buildMockOfInterface(WorksheetRepository::class);
        $this->worksheetRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->nextId);
        
        $this->clientParticipant = $this->buildMockOfClass(ClientParticipant::class);
        $this->clientParticipantRepository = $this->buildMockOfInterface(ClientParticipantRepository::class);
        $this->clientParticipantRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId, $this->clientId, $this->programParticipationId)
                ->willReturn($this->clientParticipant);
        
        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->mission->expects($this->any())
                ->method('isRootMission')
                ->willReturn(true);
        $this->missionRepository = $this->buildMockOfInterface(MissionRepository::class);
        $this->missionRepository->expects($this->any())
                ->method('aMissionInProgramWhereClientParticipate')
                ->with($this->firmId, $this->clientId, $this->programParticipationId, $this->missionId)
                ->willReturn($this->mission);
        
        $this->service = new WorksheetAddRoot(
                $this->worksheetRepository, $this->clientParticipantRepository, $this->missionRepository);
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    protected function execute()
    {
        $this->mission->expects($this->any())
                ->method('createWorksheetFormRecord')
                ->with($this->nextId, $this->formRecordData);
        return $this->service->execute($this->firmId, $this->clientId, $this->programParticipationId, $this->missionId, $this->name, $this->formRecordData);
    }
    
    public function test_addWorksheetToRepository()
    {
        $this->clientParticipant->expects($this->once())
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
