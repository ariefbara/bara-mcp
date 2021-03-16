<?php

namespace Firm\Application\Service\Manager;

use Tests\src\Firm\Application\Service\Manager\BioFormTestBase;

class EnableBioFormTest extends BioFormTestBase
{
    protected $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new EnableBioForm($this->managerRepository, $this->bioFormRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->managerId, $this->bioFormId);
    }
    public function test_execute_enableBioFormInManager()
    {
        $this->manager->expects($this->once())
                ->method("enableBioForm")
                ->with($this->bioForm);
        $this->execute();
    }
    public function test_execute_updateBioFormRepository()
    {
        $this->bioFormRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
