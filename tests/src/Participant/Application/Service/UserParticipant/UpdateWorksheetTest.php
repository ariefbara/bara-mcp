<?php

namespace Participant\Application\Service\UserParticipant;

use Participant\ {
    Application\Service\Participant\WorksheetRepository,
    Domain\Model\Participant\Worksheet
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class UpdateWorksheetTest extends TestBase
{
    protected $service;
    protected $worksheetRepository, $worksheet;
    
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
        $this->service = new UpdateWorksheet($this->worksheetRepository);
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    protected function execute()
    {
        $this->service->execute($this->userId, $this->userParticipantId, $this->worksheetId, $this->name, $this->formRecordData);
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
