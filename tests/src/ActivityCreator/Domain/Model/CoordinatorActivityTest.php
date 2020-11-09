<?php

namespace ActivityCreator\Domain\Model;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Personnel\Coordinator,
    service\ActivityDataProvider
};
use Tests\TestBase;

class CoordinatorActivityTest extends TestBase
{
    protected $coordinator;
    protected $activity;
    protected $coordinatorActivity;
    protected $id = "newId";
    protected $activityDataProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->activity = $this->buildMockOfClass(Activity::class);
        $this->coordinatorActivity = new TestableCoordinatorActivity($this->coordinator, "id", $this->activity);
        
        $this->activityDataProvider = $this->buildMockOfClass(ActivityDataProvider::class);
    }
    
    public function test_construct_setProperties()
    {
        $coordinatorActivity = new TestableCoordinatorActivity($this->coordinator, $this->id, $this->activity);
        $this->assertEquals($this->coordinator, $coordinatorActivity->coordinator);
        $this->assertEquals($this->id, $coordinatorActivity->id);
        $this->assertEquals($this->activity, $coordinatorActivity->activity);
    }
    
    public function test_update_updateActivity()
    {
        $this->activity->expects($this->once())
                ->method("update")
                ->with($this->activityDataProvider);
        $this->coordinatorActivity->update($this->activityDataProvider);
    }
}

class TestableCoordinatorActivity extends CoordinatorActivity
{
    public $coordinator;
    public $id;
    public $activity;
}
