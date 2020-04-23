<?php

namespace Firm\Application\Service\Firm;

use Firm\Domain\Model\Firm\WorksheetForm;
use Tests\TestBase;

class WorksheetFormRemoveTest extends TestBase
{
    protected $service;
    protected $worksheetFormRepository, $worksheetForm, $firmId = 'firm-id',
        $worksheetFormId = 'consultation-feedback-form-id';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->worksheetForm = $this->buildMockOfClass(WorksheetForm::class);
        $this->worksheetFormRepository = $this->buildMockOfInterface(WorksheetFormRepository::class);
        $this->worksheetFormRepository->expects($this->any())
            ->method('ofId')
            ->with($this->firmId, $this->worksheetFormId)
            ->willReturn($this->worksheetForm);
        
        $this->service = new WorksheetFormRemove($this->worksheetFormRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->worksheetFormId);
    }
    
    public function test_remove_removeWorksheetForm()
    {
        $this->worksheetForm->expects($this->once())
            ->method('remove');
        $this->execute();
    }
    public function test_remove_updateRepository()
    {
        $this->worksheetFormRepository->expects($this->once())
            ->method('update');
        $this->execute();
    }
    
}
