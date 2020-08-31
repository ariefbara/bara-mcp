<?php

namespace Participant\Application\Service\Participant;

use Participant\Domain\Model\Participant\Worksheet;
use Tests\TestBase;

class WorksheetRemoveTest extends TestBase
{
    protected $service;
    protected $worksheetRepository, $worksheet;
    
    protected $firmId = 'firmId', $clientId = 'clientId', $programId = 'programId', $worksheetId = 'worksheetId', $missionId = 'missionId';
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->worksheet = $this->buildMockOfClass(Worksheet::class);
        $this->worksheetRepository = $this->buildMockOfInterface(WorksheetRepository::class);
        $this->worksheetRepository->expects($this->any())
                ->method('aWorksheetOfClientParticipant')
                ->with($this->firmId, $this->clientId, $this->programId, $this->worksheetId)
                ->willReturn($this->worksheet);
        
        $this->service = new WorksheetRemove($this->worksheetRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->programId, $this->worksheetId);
    }
    public function test_execute_removeWorksheet()
    {
        $this->worksheet->expects($this->once())
                ->method('remove');
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->worksheetRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
