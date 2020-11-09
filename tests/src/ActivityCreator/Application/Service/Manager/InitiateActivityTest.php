<?php

namespace ActivityCreator\Application\Service\Manager;

use ActivityCreator\{
    Application\Service\ActivityTypeRepository,
    Domain\DependencyModel\Firm\Manager,
    Domain\DependencyModel\Firm\Program,
    Domain\DependencyModel\Firm\Program\ActivityType,
    Domain\Model\ManagerActivity
};
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class InitiateActivityTest extends TestBase
{

    protected $managerActivityRepository, $nextId = "nextId";
    protected $managerRepository, $manager;
    protected $programRepository, $program;
    protected $activityTypeRepository, $activityType;
    protected $dispatcher;
    protected $service;
    protected $firmId = "firmId", $managerId = "managerId", $programId = "programId", $activityTypeId = "activityTypeId";
    protected $activityDataProvider = "string represent data provider";

    protected function setUp(): void
    {
        parent::setUp();
        $this->managerActivityRepository = $this->buildMockOfInterface(ManagerActivityRepository::class);
        $this->managerActivityRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->nextId);

        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->managerRepository = $this->buildMockOfInterface(ManagerRepository::class);
        $this->managerRepository->expects($this->any())
                ->method("aManagerInFirm")
                ->with($this->firmId, $this->managerId)
                ->willReturn($this->manager);

        $this->program = $this->buildMockOfClass(Program::class);
        $this->programRepository = $this->buildMockOfInterface(ProgramRepository::class);
        $this->programRepository->expects($this->any())
                ->method("ofId")
                ->with($this->programId)
                ->willReturn($this->program);

        $this->activityType = $this->buildMockOfClass(ActivityType::class);
        $this->activityTypeRepository = $this->buildMockOfInterface(ActivityTypeRepository::class);
        $this->activityTypeRepository->expects($this->any())
                ->method("ofId")
                ->with($this->activityTypeId)
                ->willReturn($this->activityType);

        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);

        $this->service = new InitiateActivity(
                $this->managerActivityRepository, $this->managerRepository, $this->programRepository,
                $this->activityTypeRepository, $this->dispatcher);
    }

    protected function execute()
    {
        return $this->service->execute(
                        $this->firmId, $this->managerId, $this->programId, $this->activityTypeId,
                        $this->activityDataProvider);
    }

    public function test_execute_addActivityToRepository()
    {
        $this->manager->expects($this->once())
                ->method("initiateActivityInProgram")
                ->with($this->nextId, $this->program, $this->activityType, $this->activityDataProvider);
        $this->managerActivityRepository->expects($this->once())
                ->method("add");
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }

    public function test_execute_dispatcheActivity()
    {
        $managerActivity = $this->buildMockOfClass(ManagerActivity::class);
        $this->manager->expects($this->once())
                ->method("initiateActivityInProgram")
                ->willReturn($managerActivity);
        $this->dispatcher->expects($this->once())
                ->method("dispatch")
                ->with($managerActivity);
        $this->execute();
    }

}
