<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\ {
    Model\Firm\Manager,
    Model\Firm\Program,
    Service\ActivityTypeDataProvider
};
use Tests\TestBase;

class CreateActivityTypeTest extends TestBase
{

    protected $activityTypeRepository, $nextId = "nextId";
    protected $managerRepository, $manager;
    protected $programRepository, $program;
    protected $service;
    protected $firmId = "firmId", $managerId = "managerId", $programId = "programId", $activityTypeDataProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->activityTypeRepository = $this->buildMockOfInterface(ActivityTypeRepository::class);
        $this->activityTypeRepository->expects($this->any())
                ->method("nextIdentity")
                ->willReturn($this->nextId);

        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->managerRepository = $this->buildMockOfInterface(ManagerRepository::class);
        $this->managerRepository->expects($this->any())
                ->method("ofId")
                ->with($this->firmId, $this->managerId)
                ->willReturn($this->manager);

        $this->program = $this->buildMockOfClass(Program::class);
        $this->programRepository = $this->buildMockOfInterface(ProgramRepository::class);
        $this->programRepository->expects($this->any())
                ->method("aProgramOfId")
                ->with($this->programId)
                ->willReturn($this->program);

        $this->service = new CreateActivityType(
                $this->activityTypeRepository, $this->managerRepository, $this->programRepository);
        
        $this->activityTypeDataProvider = $this->buildMockOfClass(ActivityTypeDataProvider::class);
    }
    
    public function execute()
    {
        return $this->service->execute($this->firmId, $this->managerId, $this->programId, $this->activityTypeDataProvider);
    }
    public function test_execute_addActivityTypeToRepository()
    {
        $this->manager->expects($this->once())
                ->method("createActivityTypeInProgram")
                ->with($this->program, $this->nextId, $this->activityTypeDataProvider);
        $this->activityTypeRepository->expects($this->once())
                ->method("add");
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }

}
