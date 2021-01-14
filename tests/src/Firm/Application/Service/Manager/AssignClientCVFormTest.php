<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\Manager;
use Tests\TestBase;

class AssignClientCVFormTest extends TestBase
{
    protected $managerRepository, $manager;
    protected $profileFormRepository;
    protected $service;
    protected $firmId = "firmId", $managerId = "managerId", $profileFormId = "profileFormId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->managerRepository = $this->buildMockOfInterface(ManagerRepository::class);
        $this->managerRepository->expects($this->any())
                ->method("aManagerInFirm")
                ->with($this->firmId, $this->managerId)
                ->willReturn($this->manager);
        
        $this->profileFormRepository = $this->buildMockOfInterface(ProfileFormRepository::class);
        
        $this->service = new AssignClientCVForm($this->managerRepository, $this->profileFormRepository);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->managerId, $this->profileFormId);
    }
    public function test_execute_assigneClientCVFormInManager()
    {
        $this->profileFormRepository->expects($this->once())->method("ofId")->with($this->profileFormId);
        $this->manager->expects($this->once())
                ->method("assignClientCVForm");
        $this->execute();
    }
    public function test_execute_returnAssignedId()
    {
        $this->manager->expects($this->once())
                ->method("assignClientCVForm")
                ->willReturn($id = "clientCVFormId");
        $this->assertEquals($id, $this->execute());
    }
    public function test_execute_updateRepository()
    {
        $this->managerRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
