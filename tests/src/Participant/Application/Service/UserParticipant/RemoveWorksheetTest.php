<?php

namespace Participant\Application\Service\UserParticipant;

use Participant\ {
    Application\Service\Participant\WorksheetRepository,
    Domain\Model\Participant\Worksheet
};
use Tests\TestBase;

class RemoveWorksheetTest extends TestBase
{
    protected $service;
    protected $worksheetRepository, $worksheet;
    
    protected $userId = 'userId', $userParticipantId = 'userParticipantId', $worksheetId = 'worksheetId', $missionId = 'missionId';
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->worksheet = $this->buildMockOfClass(Worksheet::class);
        $this->worksheetRepository = $this->buildMockOfInterface(WorksheetRepository::class);
        $this->worksheetRepository->expects($this->any())
                ->method('aWorksheetBelongsToUserParticipant')
                ->with($this->userId, $this->userParticipantId, $this->worksheetId)
                ->willReturn($this->worksheet);
        
        $this->service = new RemoveWorksheet($this->worksheetRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->userId, $this->userParticipantId, $this->worksheetId);
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
