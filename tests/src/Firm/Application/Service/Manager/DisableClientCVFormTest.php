<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\Manager;
use Tests\TestBase;

class DisableClientCVFormTest extends TestBase
{
    protected $clientCVFormRepository;
    protected $managerRepository, $manager;
    protected $service;
    protected $firmId = "firmId", $managerId = "managerId", $clientCVFormId = "clientCVFormId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->clientCVFormRepository = $this->buildMockOfInterface(ClientCVFormRepository::class);
        
        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->managerRepository = $this->buildMockOfInterface(ManagerRepository::class);
        $this->managerRepository->expects($this->any())
                ->method("aManagerInFirm")
                ->with($this->firmId, $this->managerId)
                ->willReturn($this->manager);
        
        $this->service = new DisableClientCVForm($this->clientCVFormRepository, $this->managerRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->managerId, $this->clientCVFormId);
    }
    public function test_execute_managerDisableClientCVForm()
    {
        $this->clientCVFormRepository->expects($this->once())->method("ofId")->with($this->clientCVFormId);
        $this->manager->expects($this->once())
                ->method("disableClientCVForm");
        $this->execute();
    }
    public function test_execute_update_repository()
    {
        $this->clientCVFormRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
