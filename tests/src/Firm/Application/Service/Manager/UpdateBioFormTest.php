<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Shared\FormData;
use Tests\src\Firm\Application\Service\Manager\BioFormTestBase;
use User\Domain\Model\Manager;

class UpdateBioFormTest extends BioFormTestBase
{
    protected $service;
    protected $formData;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->service = new UpdateBioForm($this->managerRepository, $this->bioFormRepository);
        $this->formData = $this->buildMockOfClass(FormData::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->managerId, $this->bioFormId, $this->formData);
    }
    public function test_execute_managerUpdateBioForm()
    {
        $this->manager->expects($this->once())
                ->method("updateBioForm")
                ->with($this->bioForm, $this->formData);
        $this->execute();
    }
    public function test_execute_updateBioFormRepository()
    {
        $this->bioFormRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
