<?php

namespace Client\Application\Service\Client\ProgramParticipation;

use Client\ {
    Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId,
    Application\Service\Client\ProgramParticipation\WorksheetRepository,
    Application\Service\Client\ProgramParticipation\WorksheetUpdate,
    Domain\Model\Client\ProgramParticipation\Worksheet
};
use Shared\Domain\Model\FormRecordData;
use Tests\TestBase;

class WorksheetUpdateTest extends TestBase
{
    protected $service;
    protected $programParticipationCompositionId;
    protected $worksheetRepository, $worksheet, $worksheetId = 'worksheetId';
    protected $formRecordData, $name = 'new worksheet name';
    
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
        $this->service = new WorksheetUpdate($this->worksheetRepository);
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    protected function execute()
    {
        $this->service->execute($this->programParticipationCompositionId, $this->worksheetId, $this->name, $this->formRecordData);
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
