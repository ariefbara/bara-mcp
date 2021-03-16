<?php

namespace Bara\Application\Service;

use Tests\src\Bara\Application\Service\WorksheetFormTestBase;

class RemoveWorksheetFormTest extends WorksheetFormTestBase
{
    protected $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RemoveWorksheetForm($this->adminRepository, $this->worksheetFormRepository);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->adminId, $this->worksheetFormId);
    }
    public function test_execute_adminRemoveWorksheetForm()
    {
        $this->admin->expects($this->once())
                ->method('removeWorksheetForm')
                ->with($this->worksheetForm);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->adminRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
