<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\Manager;
use Query\Application\Service\Firm\BioFormRepository;
use Tests\src\Firm\Application\Service\Manager\BioFormTestBase;

class DisableBioFormTest extends BioFormTestBase
{
    protected $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DisableBioForm($this->managerRepository, $this->bioFormRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->managerId, $this->bioFormId);
    }
    public function test_execute_managerDisableClientCVForm()
    {
        $this->manager->expects($this->once())
                ->method("disableBioForm")
                ->with($this->bioForm);
        $this->execute();
    }
    public function test_execute_update_repository()
    {
        $this->bioFormRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
