<?php

namespace ActivityCreator\Domain\Model;

use ActivityCreator\Domain\{
    DependencyModel\Firm\Manager,
    service\ActivityDataProvider
};
use Tests\TestBase;

class ManagerActivityTest extends TestBase
{

    protected $manager;
    protected $activity;
    protected $managerActivity;
    protected $id = "newId";
    protected $activityDataProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->activity = $this->buildMockOfClass(Activity::class);
        $this->managerActivity = new TestableManagerActivity($this->manager, "id", $this->activity);

        $this->activityDataProvider = $this->buildMockOfClass(ActivityDataProvider::class);
    }

    public function test_construct_setProperties()
    {
        $managerActivity = new TestableManagerActivity($this->manager, $this->id, $this->activity);
        $this->assertEquals($this->manager, $managerActivity->manager);
        $this->assertEquals($this->id, $managerActivity->id);
        $this->assertEquals($this->activity, $managerActivity->activity);
    }

    public function test_update_updateActivity()
    {
        $this->activity->expects($this->once())
                ->method("update")
                ->with($this->activityDataProvider);
        $this->managerActivity->update($this->activityDataProvider);
    }

}

class TestableManagerActivity extends ManagerActivity
{

    public $manager;
    public $id;
    public $activity;

}
