<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\Manager;
use Firm\Domain\Model\Shared\FormData;
use Tests\src\Firm\Application\Service\Manager\BioFormTestBase;

class CreateBioFormTest extends BioFormTestBase
{
    protected $service;
    protected $formData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CreateBioForm($this->managerRepository, $this->bioFormRepository);
        $this->formData = $this->buildMockOfClass(FormData::class);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->managerId, $this->formData);
    }
    public function test_execute_addBioFormToRepository()
    {
        $this->bioFormRepository->expects($this->once())->method("add");
        $this->manager->expects($this->once())
                ->method("createBioForm")
                ->with($this->nextId, $this->formData);
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }
}
