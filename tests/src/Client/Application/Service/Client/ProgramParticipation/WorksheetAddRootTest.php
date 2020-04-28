<?php

namespace Client\Application\Service\Client\ProgramParticipation;

use Client\ {
    Application\Service\Client\ProgramParticipation\WorksheetAddRoot,
    Application\Service\Client\ProgramParticipation\WorksheetRepository,
    Application\Service\Client\ProgramParticipationRepository,
    Application\Service\Firm\Program\MissionRepository,
    Domain\Model\Client\ProgramParticipation,
    Domain\Model\Client\ProgramParticipation\Worksheet,
    Domain\Model\Firm\Program\Mission
};
use Shared\Domain\Model\ {
    FormRecord,
    FormRecordData
};
use Tests\TestBase;

class WorksheetAddRootTest extends TestBase
{
    protected $service;
    protected $clientId = "clientId";
    protected $worksheetRepository;
    protected $programParticipationRepository, $programParticipation, $programParticipationId = "programParticipationId";
    protected $missionRepository, $mission, $missionId = 'missionId', $formRecord;
    protected $nextId = 'nextId';
    protected $name = "new worksheet name", $formRecordData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->worksheetRepository = $this->buildMockOfInterface(WorksheetRepository::class);
        $this->worksheetRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->nextId);
        
        $this->programParticipationRepository = $this->buildMockOfInterface(ProgramParticipationRepository::class);
        $this->programParticipation = $this->buildMockOfClass(ProgramParticipation::class);
        $this->programParticipationRepository->expects($this->any())
                ->method('ofId')
                ->with($this->clientId, $this->programParticipationId)
                ->willReturn($this->programParticipation);
        
        $this->missionRepository = $this->buildMockOfInterface(MissionRepository::class);
        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->mission->expects($this->any())
                ->method('isRootMission')
                ->willReturn(true);
        $this->missionRepository->expects($this->any())
                ->method('ofId')
                ->with($this->clientId, $this->programParticipationId, $this->missionId)
                ->willReturn($this->mission);
        
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
        
        $this->service = new WorksheetAddRoot(
                $this->worksheetRepository, $this->programParticipationRepository, $this->missionRepository);
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    protected function execute()
    {
        $this->mission->expects($this->any())
                ->method('createWorksheetFormRecord')
                ->with($this->nextId, $this->formRecordData)
                ->willReturn($this->formRecord);
        return $this->service->execute($this->clientId, $this->programParticipationId, $this->missionId, $this->name, $this->formRecordData);
    }
    
    public function test_addWorksheetToRepository()
    {
        $worksheet = Worksheet::createRootWorksheet($this->programParticipation, $this->nextId, $this->name, $this->mission, $this->formRecord);
        $this->worksheetRepository->expects($this->once())
                ->method('add')
                ->with($worksheet);
        $this->execute();
    }
}
