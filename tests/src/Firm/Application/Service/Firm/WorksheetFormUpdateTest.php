<?php

namespace Firm\Application\Service\Firm;

use Firm\Domain\Model\ {
    Firm\WorksheetForm,
    Shared\FormData
};
use Tests\TestBase;

class WorksheetFormUpdateTest extends TestBase
{

    protected $worksheetFormRepository, $worksheetForm, $firmId = 'firm-id',
        $worksheetFormId = 'consultation-feedback-form-id';
    
    protected $service;
    protected $formData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->worksheetForm = $this->buildMockOfClass(WorksheetForm::class);
        $this->worksheetFormRepository = $this->buildMockOfInterface(WorksheetFormRepository::class);
        $this->worksheetFormRepository->expects($this->any())
            ->method('ofId')
            ->with($this->firmId, $this->worksheetFormId)
            ->willReturn($this->worksheetForm);

        $this->service = new WorksheetFormUpdate($this->worksheetFormRepository);
        $this->formData = $this->buildMockOfClass(FormData::class);
        $this->formData->expects($this->any())
                ->method('getName')
                ->willReturn('new form name');
    }

    protected function execute()
    {
        $this->service->execute($this->firmId, $this->worksheetFormId, $this->formData);
    }
    public function test_execute_updateWorksheetForm()
    {
        $this->worksheetForm->expects($this->once())
            ->method('update')
            ->with($this->formData);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->worksheetFormRepository->expects($this->once())
            ->method('update');
        $this->execute();
    }
    
}
