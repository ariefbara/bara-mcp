<?php

namespace ActivityCreator\Application\Service\Manager;

use ActivityCreator\Domain\ {
    Model\ManagerActivity,
    service\ActivityDataProvider
};
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class UpdateActivityTest extends TestBase
{
    protected $managerActivityRepository, $managerActivity;
    protected $dispatcher;
    protected $service;
    protected $firmId = "firmId", $managerId = "managerId", $managerActivityId = "managerActivityId";
    protected $activityDataProvider = "string represent data provider";

    protected function setUp(): void
    {
        parent::setUp();
        $this->managerActivity = $this->buildMockOfClass(ManagerActivity::class);
        $this->managerActivityRepository = $this->buildMockOfInterface(ManagerActivityRepository::class);
        $this->managerActivityRepository->expects($this->any())
                ->method('aManagerActivityOfId')
                ->with($this->firmId, $this->managerId, $this->managerActivityId)
                ->willReturn($this->managerActivity);

        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);

        $this->service = new UpdateActivity($this->managerActivityRepository, $this->dispatcher);
        $this->activityDataProvider = $this->buildMockOfClass(ActivityDataProvider::class);
    }

    protected function execute()
    {
        $this->service->execute($this->firmId, $this->managerId, $this->managerActivityId, $this->activityDataProvider);
    }
    public function test_execute_updateManagerActivity()
    {
        $this->managerActivity->expects($this->once())
                ->method("update")
                ->with($this->activityDataProvider);
        $this->execute();
    }
    public function test_updateRepository()
    {
        $this->managerActivityRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
    public function test_execute_dispathceManagerActivity()
    {
        $this->dispatcher->expects($this->once())
                ->method("dispatch");
        $this->execute();
    }
}
