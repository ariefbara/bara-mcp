<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\Manager;
use Firm\Domain\Model\Shared\FormData;
use Tests\TestBase;

class UpdateProfileFormTest extends TestBase
{
    protected $profileFormRepository;
    protected $managerRepository, $manager;
    protected $service;
    protected $firmId = "firmId", $managerId = "managerId", $profileFormId = "profileFormId";
    protected $formData;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->profileFormRepository = $this->buildMockOfInterface(ProfileFormRepository::class);
        
        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->managerRepository = $this->buildMockOfInterface(ManagerRepository::class);
        $this->managerRepository->expects($this->any())
                ->method("aManagerInFirm")
                ->with($this->firmId, $this->managerId)
                ->willReturn($this->manager);
        
        $this->service = new UpdateProfileForm($this->profileFormRepository, $this->managerRepository);
        
        $this->formData = $this->buildMockOfClass(FormData::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->managerId, $this->profileFormId, $this->formData);
    }
    public function test_execute_managerUpdateProfileForm()
    {
        $this->profileFormRepository->expects($this->once())
                ->method("ofId")
                ->with($this->profileFormId);
        
        $this->manager->expects($this->once())
                ->method("updateProfileForm")
                ->with($this->anything(), $this->formData);
        
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->profileFormRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
