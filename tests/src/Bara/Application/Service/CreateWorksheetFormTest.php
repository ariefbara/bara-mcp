<?php

namespace Bara\Application\Service;

use Firm\Domain\Model\Shared\FormData;
use Tests\src\Bara\Application\Service\WorksheetFormTestBase;

class CreateWorksheetFormTest extends WorksheetFormTestBase
{
    protected $service;
    protected $formData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->worksheetFormRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->worksheetFormId);
        $this->service = new CreateWorksheetForm($this->adminRepository, $this->worksheetFormRepository);
        $this->formData = $this->buildMockOfClass(FormData::class);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->adminId, $this->formData);
    }
    public function test_execute_addWorksheetFormToRepository()
    {
        $this->admin->expects($this->once())
                ->method('createWorksheetForm')
                ->with($this->worksheetFormId, $this->formData)
                ->willReturn($this->worksheetForm);
        $this->worksheetFormRepository->expects($this->once())
                ->method('add')
                ->with($this->worksheetForm);
        $this->execute();
    }
    public function test_execute_returnWorksheetFormId()
    {
        $this->assertEquals($this->worksheetFormId, $this->execute());
    }
}
