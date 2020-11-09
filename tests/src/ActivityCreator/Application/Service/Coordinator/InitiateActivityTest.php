<?php

namespace ActivityCreator\Application\Service\Coordinator;

use ActivityCreator\ {
    Application\Service\ActivityTypeRepository,
    Domain\DependencyModel\Firm\Personnel\Coordinator,
    Domain\DependencyModel\Firm\Program\ActivityType,
    Domain\Model\CoordinatorActivity
};
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class InitiateActivityTest extends TestBase
{
    protected $coordinatorActivityRepository, $nextId = "nextId";
    protected $coordinatorRepository, $coordinator;
    protected $activityTypeRepository, $activityType;
    protected $dispatcher;
    protected $service;
    protected $firmId = "firmId", $personnelId = "personnelId", $coordinatorId = "coordinatorId", $activityTypeId = "activityTypeId";
    protected $activityDataProvider = "string represent data provider";

    protected function setUp(): void
    {
        parent::setUp();
        $this->coordinatorActivityRepository = $this->buildMockOfInterface(CoordinatorActivityRepository::class);
        $this->coordinatorActivityRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->nextId);

        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->coordinatorRepository = $this->buildMockOfInterface(CoordinatorRepository::class);
        $this->coordinatorRepository->expects($this->any())
                ->method("aCoordinatorBelongsToPersonnel")
                ->with($this->firmId, $this->personnelId, $this->coordinatorId)
                ->willReturn($this->coordinator);

        $this->activityType = $this->buildMockOfClass(ActivityType::class);
        $this->activityTypeRepository = $this->buildMockOfInterface(ActivityTypeRepository::class);
        $this->activityTypeRepository->expects($this->any())
                ->method("ofId")
                ->with($this->activityTypeId)
                ->willReturn($this->activityType);

        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);

        $this->service = new InitiateActivity(
                $this->coordinatorActivityRepository, $this->coordinatorRepository, $this->activityTypeRepository,
                $this->dispatcher);
    }

    protected function execute()
    {
        return $this->service->execute(
                        $this->firmId, $this->personnelId, $this->coordinatorId, $this->activityTypeId,
                        $this->activityDataProvider);
    }
    public function test_execute_addActivityToRepository()
    {
        $this->coordinator->expects($this->once())
                ->method("initiateActivity")
                ->with($this->nextId, $this->activityType, $this->activityDataProvider);
        $this->coordinatorActivityRepository->expects($this->once())
                ->method("add");
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }
    public function test_execute_dispatcheActivity()
    {
        $coordinatorActivity = $this->buildMockOfClass(CoordinatorActivity::class);
        $this->coordinator->expects($this->once())
                ->method("initiateActivity")
                ->willReturn($coordinatorActivity);
        $this->dispatcher->expects($this->once())
                ->method("dispatch")
                ->with($coordinatorActivity);
        $this->execute();
    }

}
