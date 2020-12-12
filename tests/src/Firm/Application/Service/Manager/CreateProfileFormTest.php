<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\Manager;
use Firm\Domain\Model\Shared\FormData;
use Tests\TestBase;

class CreateProfileFormTest extends TestBase
{
    protected $profileFormRepository, $nextId = "nextId";
    protected $managerRepository, $manager;
    protected $service;
    protected $firmId = "firmId", $managerId = "managerId", $formData;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->profileFormRepository = $this->buildMockOfInterface(ProfileFormRepository::class);
        $this->profileFormRepository->expects($this->any())
                ->method("nextIdentity")
                ->willReturn($this->nextId);
        
        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->managerRepository = $this->buildMockOfInterface(ManagerRepository::class);
        $this->managerRepository->expects($this->any())
                ->method("aManagerInFirm")
                ->with($this->firmId, $this->managerId)
                ->willReturn($this->manager);
        
        $this->service = new CreateProfileForm($this->profileFormRepository, $this->managerRepository);
        
        $this->formData = $this->buildMockOfClass(FormData::class);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->managerId, $this->formData);
    }
    public function test_execute_addProfileFormCreatedByManagerToRepository()
    {
        $this->manager->expects($this->once())
                ->method("createProfileForm")
                ->with($this->nextId, $this->formData);
        $this->profileFormRepository->expects($this->once())
                ->method("add");
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }
}
