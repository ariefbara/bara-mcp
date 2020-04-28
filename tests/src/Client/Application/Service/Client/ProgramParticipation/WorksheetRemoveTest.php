<?php

namespace Client\Application\Service\Client\ProgramParticipation;

use Client\ {
    Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId,
    Application\Service\Client\ProgramParticipation\WorksheetRemove,
    Application\Service\Client\ProgramParticipation\WorksheetRepository,
    Domain\Model\Client\ProgramParticipation\Worksheet
};
use Tests\TestBase;

class WorksheetRemoveTest extends TestBase
{
    protected $service;
    protected $programParticipationCompositionId;
    protected $worksheetRepository, $worksheet, $worksheetId = 'worksheetId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->programParticipationCompositionId = $this->buildMockOfClass(ProgramParticipationCompositionId::class);
        $this->worksheetRepository = $this->buildMockOfInterface(WorksheetRepository::class);
        $this->worksheet = $this->buildMockOfClass(Worksheet::class);
        $this->worksheetRepository->expects($this->any())
                ->method('ofId')
                ->with($this->programParticipationCompositionId, $this->worksheetId)
                ->willReturn($this->worksheet);
        $this->service = new WorksheetRemove($this->worksheetRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->programParticipationCompositionId, $this->worksheetId);
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
