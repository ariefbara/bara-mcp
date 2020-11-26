<?php

namespace User\Application\Service\Manager;

use Resources\Application\Event\Dispatcher;
use Tests\TestBase;
use User\Domain\Model\Manager;

class GenerateResetPasswordCodeTest extends TestBase
{
    protected $managerRepository, $manager;
    protected $dispatcher;
    protected $service;
    protected $firmIdentifier = "firmIdentifier", $email = "manager@email.org";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->managerRepository = $this->buildMockOfInterface(ManagerRepository::class);
        $this->managerRepository->expects($this->once())
                ->method("aManagerInFirmByEmailAndIdentifier")
                ->with($this->firmIdentifier, $this->email)
                ->willReturn($this->manager);
        
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);
        
        $this->service = new GenerateResetPasswordCode($this->managerRepository, $this->dispatcher);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmIdentifier, $this->email);
    }
    public function test_execute_generateManagerResetPasswordCode()
    {
        $this->manager->expects($this->once())
                ->method("generateResetPasswordCode");
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->managerRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
    public function test_execute_dispatchManager()
    {
        $this->dispatcher->expects($this->once())
                ->method("dispatch")
                ->with($this->manager);
        
        $this->execute();
    }
}
