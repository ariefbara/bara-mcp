<?php

namespace ActivityCreator\Domain\Model;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Personnel\Consultant,
    service\ActivityDataProvider
};
use Tests\TestBase;

class ConsultantActivityTest extends TestBase
{
    protected $consultant;
    protected $activity;
    protected $consultantActivity;
    protected $id = "newId";
    protected $activityDataProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->activity = $this->buildMockOfClass(Activity::class);
        $this->consultantActivity = new TestableConsultantActivity($this->consultant, "id", $this->activity);
        
        $this->activityDataProvider = $this->buildMockOfClass(ActivityDataProvider::class);
    }
    
    public function test_construct_setProperties()
    {
        $consultantActivity = new TestableConsultantActivity($this->consultant, $this->id, $this->activity);
        $this->assertEquals($this->consultant, $consultantActivity->consultant);
        $this->assertEquals($this->id, $consultantActivity->id);
        $this->assertEquals($this->activity, $consultantActivity->activity);
    }
    
    public function test_update_updateActivity()
    {
        $this->activity->expects($this->once())
                ->method("update")
                ->with($this->activityDataProvider);
        $this->consultantActivity->update($this->activityDataProvider);
    }
}

class TestableConsultantActivity extends ConsultantActivity
{
    public $consultant;
    public $id;
    public $activity;
}
