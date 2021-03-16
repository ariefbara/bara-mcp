<?php

namespace Bara\Application\Service;

use Firm\Domain\Model\Shared\FormData;
use Tests\src\Bara\Application\Service\WorksheetFormTestBase;

class UpdateWorksheetFormTest extends WorksheetFormTestBase
{
    protected $service;
    protected $formData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UpdateWorksheetForm($this->adminRepository, $this->worksheetFormRepository);
        $this->formData = $this->buildMockOfClass(FormData::class);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->adminId, $this->worksheetFormId, $this->formData);
    }
    public function test_execute_adminUpdateWorksheetForm()
    {
        $this->admin->expects($this->once())
                ->method('updateWorksheetForm')
                ->with($this->worksheetForm, $this->formData);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->adminRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
