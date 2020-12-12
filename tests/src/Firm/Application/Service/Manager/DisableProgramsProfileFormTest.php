<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\Manager;
use Tests\TestBase;

class DisableProgramsProfileFormTest extends TestBase
{
    protected $programsProfileFormRepository;
    protected $managerRepository, $manager;
    protected $service;
    protected $firmId = "firmId", $managerId = "managerId", $programsProfileFormId = "programsProfileFormId";
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->programsProfileFormRepository = $this->buildMockOfInterface(ProgramsProfileFormRepository::class);
        
        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->managerRepository = $this->buildMockOfInterface(ManagerRepository::class);
        $this->managerRepository->expects($this->any())
                ->method("aManagerInFirm")
                ->with($this->firmId, $this->managerId)
                ->willReturn($this->manager);
        
        $this->service = new DisableProgramsProfileForm($this->programsProfileFormRepository, $this->managerRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->managerId, $this->programsProfileFormId);
    }
    public function test_execute_managerDisableProgramsProfileForm()
    {
        $this->manager->expects($this->once())
                ->method("disableProgramsProfileForm");
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->programsProfileFormRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
