<?php

namespace User\Application\Service\Manager;

use Tests\TestBase;
use User\Domain\Model\Manager;

class ResetPasswordTest extends TestBase
{
    protected $managerRepository, $manager;
    protected $service;
    protected $firmIdentifier = "firmIdentifier", $email = "manager@email.org", $resetPasswordCode = "resetPasswordCode", 
            $password = "newPwd123";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->managerRepository = $this->buildMockOfInterface(ManagerRepository::class);
        $this->managerRepository->expects($this->once())
                ->method("aManagerInFirmByEmailAndIdentifier")
                ->with($this->firmIdentifier, $this->email)
                ->willReturn($this->manager);
        
        $this->service = new ResetPassword($this->managerRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmIdentifier, $this->email, $this->resetPasswordCode, $this->password);
    }
    public function test_execute_resetManagerPassword()
    {
        $this->manager->expects($this->once())
                ->method("resetPassword")
                ->with($this->resetPasswordCode, $this->password);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->managerRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
