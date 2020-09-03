<?php

namespace Participant\Application\Service\ClientParticipant;

use Participant\ {
    Application\Service\Participant\WorksheetRepository,
    Domain\Model\Participant\Worksheet
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class WorksheetUpdateTest extends TestBase
{
    protected $service;
    protected $worksheetRepository, $worksheet;
    
    protected $firmId = 'firmId', $clientId = 'clientId', $programId = 'programId', $worksheetId = 'worksheetId', $missionId = 'missionId';
    protected $name = 'new worksheet name', $formRecordData;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->worksheet = $this->buildMockOfClass(Worksheet::class);
        $this->worksheetRepository = $this->buildMockOfInterface(WorksheetRepository::class);
        $this->worksheetRepository->expects($this->any())
                ->method('aWorksheetBelongsToClientParticipant')
                ->with($this->firmId, $this->clientId, $this->programId, $this->worksheetId)
                ->willReturn($this->worksheet);
        $this->service = new WorksheetUpdate($this->worksheetRepository);
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->programId, $this->worksheetId, $this->name, $this->formRecordData);
    }
    public function test_execute_updateWorksheet()
    {
        $this->worksheet->expects($this->once())
                ->method('update')
                ->with($this->name, $this->formRecordData);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->worksheetRepository->expects($this->once())
                ->method('update');

        $this->execute();
    }
}
