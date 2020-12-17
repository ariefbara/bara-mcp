<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\Manager;
use Tests\TestBase;

class RemoveProgramTest extends TestBase
{
    protected $programRepository;
    protected $managerRepository, $manager;
    protected $service;
    protected $firmId = "firmId", $managerId = "managerId", $programId = "programId";
    
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
        
        $this->service = new RemoveProgram($this->programRepository, $this->managerRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->managerId, $this->programId);
    }
    public function test_execute_managerRemoveProgram()
    {
        $this->programRepository->expects($this->once())->method("aProgramOfId")->with($this->programId);
        
        $this->manager->expects($this->once())
                ->method("removeProgram");
        
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->programRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
