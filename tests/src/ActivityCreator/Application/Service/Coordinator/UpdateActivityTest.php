<?php

namespace ActivityCreator\Application\Service\Coordinator;

use ActivityCreator\Domain\ {
    Model\CoordinatorActivity,
    service\ActivityDataProvider
};
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class UpdateActivityTest extends TestBase
{

    protected $coordinatorActivityRepository, $coordinatorActivity;
    protected $dispatcher;
    protected $service;
    protected $firmId = "firmId", $personnelId = "personnelId", $coordinatorActivityId = "coordinatorActivityId";
    protected $activityDataProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->coordinatorActivity = $this->buildMockOfClass(CoordinatorActivity::class);
        $this->coordinatorActivityRepository = $this->buildMockOfInterface(CoordinatorActivityRepository::class);
        $this->coordinatorActivityRepository->expects($this->any())
                ->method('aCoordinatorActivityBelongsToPersonnel')
                ->with($this->firmId, $this->personnelId, $this->coordinatorActivityId)
                ->willReturn($this->coordinatorActivity);

        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);

        $this->service = new UpdateActivity($this->coordinatorActivityRepository, $this->dispatcher);
        $this->activityDataProvider = $this->buildMockOfClass(ActivityDataProvider::class);
    }

    protected function execute()
    {
        $this->service->execute(
                $this->firmId, $this->personnelId, $this->coordinatorActivityId, $this->activityDataProvider);
    }
    public function test_execute_updateCoordinatorActivity()
    {
        $this->coordinatorActivity->expects($this->once())
                ->method("update")
                ->with($this->activityDataProvider);
        $this->execute();
    }
    public function test_updateRepository()
    {
        $this->coordinatorActivityRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }

    public function test_execute_dispathceCoordinatorActivity()
    {
        $this->dispatcher->expects($this->once())
                ->method("dispatch");
        $this->execute();
    }

}
