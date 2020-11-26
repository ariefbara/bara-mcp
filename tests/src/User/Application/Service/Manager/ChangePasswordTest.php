<?php

namespace User\Application\Service\Manager;

use Tests\TestBase;
use User\Domain\Model\Manager;

class ChangePasswordTest extends TestBase
{
    protected $managerRepository, $manager;
    protected $service;
    protected $firmId = "firmId", $managerId = "managerId", $oldPassword = "oldPassword123", $newPassword = "newPwd123";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->managerRepository = $this->buildMockOfInterface(ManagerRepository::class);
        $this->managerRepository->expects($this->any())
                ->method("aManagerInFirm")
                ->with($this->firmId, $this->managerId)
                ->willReturn($this->manager);
        
        $this->service = new ChangePassword($this->managerRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->managerId, $this->oldPassword, $this->newPassword);
    }
    public function test_execute_updateManagerPassword()
    {
        $this->manager->expects($this->once())
                ->method("changePassword")
                ->with($this->oldPassword, $this->newPassword);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->managerRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
