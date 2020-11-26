<?php

namespace Notification\Application\Service;

use Notification\Domain\Model\Firm\Manager;
use Tests\TestBase;

class CreateManagerResetPasswordMailTest extends TestBase
{
    protected $managerMailRepository, $nextId = "nextId";
    protected $managerRepository, $manager;
    protected $service;
    protected $managerId = "managerId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->managerMailRepository = $this->buildMockOfInterface(ManagerMailRepository::class);
        $this->managerMailRepository->expects($this->any())
                ->method("nextIdentity")
                ->willReturn($this->nextId);
        
        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->managerRepository = $this->buildMockOfInterface(ManagerRepository::class);
        $this->managerRepository->expects($this->any())
                ->method("ofId")
                ->with($this->managerId)
                ->willReturn($this->manager);
        
        $this->service = new CreateManagerResetPasswordMail($this->managerMailRepository, $this->managerRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->managerId);
    }
    public function test_execute_addManangerMailToRepository()
    {
        $this->manager->expects($this->once())
                ->method("createResetPasswordMail");
        
        $this->managerMailRepository->expects($this->once())
                ->method("add");
        
        $this->execute();
    }
    
}
