<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\Manager;
use Tests\TestBase;

class AssignProfileFormToProgramTest extends TestBase
{
    protected $programRepository;
    protected $managerRepository, $manager;
    protected $pofileFormRepository;
    protected $service;
    protected $firmId = "firmId", $managerId = "managerId", $programId = "programId", $profileFormId = "profileFormId";
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->programRepository = $this->buildMockOfInterface(ProgramRepository::class);
        
        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->managerRepository = $this->buildMockOfInterface(ManagerRepository::class);
        $this->managerRepository->expects($this->any())
                ->method("aManagerInFirm")
                ->with($this->firmId, $this->managerId)
                ->willReturn($this->manager);
        
        $this->profileFormRepository = $this->buildMockOfInterface(ProfileFormRepository::class);
        
        $this->service = new AssignProfileFormToProgram($this->programRepository, $this->managerRepository, $this->profileFormRepository);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->managerId, $this->programId, $this->profileFormId);
    }
    public function test_execute_managerAssignProfileFormToProgram()
    {
        $this->programRepository->expects($this->once())->method("aProgramOfId");
        $this->profileFormRepository->expects($this->once())->method("ofId");
        
        $this->manager->expects($this->once())
                ->method("assignProfileFormToProgram");
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->programRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
    public function test_execute_returnManagerAssignProfileFormToProgramResult()
    {
        $this->manager->expects($this->once())
                ->method("assignProfileFormToProgram")
                ->willReturn($id = "programsProfileFormId");
        $this->assertEquals($id, $this->execute());
    }
}
