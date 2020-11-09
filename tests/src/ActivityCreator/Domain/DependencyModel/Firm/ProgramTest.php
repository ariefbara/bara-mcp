<?php

namespace ActivityCreator\Domain\DependencyModel\Firm;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Program\ActivityType,
    Model\Activity,
    service\ActivityDataProvider
};
use DateTimeImmutable;
use Tests\TestBase;

class ProgramTest extends TestBase
{
    protected $program;
    protected $activityId = "activityId", $activityType, $activityDataProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = new TestableProgram();
        
        $this->activityType = $this->buildMockOfClass(ActivityType::class);
        $this->activityDataProvider = $this->buildMockOfClass(ActivityDataProvider::class);
        $this->activityDataProvider->expects($this->any())->method("getName")->willReturn("name");
        $this->activityDataProvider->expects($this->any())->method("getStartTime")->willReturn(new DateTimeImmutable());
        $this->activityDataProvider->expects($this->any())->method("getEndTime")->willReturn(new DateTimeImmutable());
    }
    
    protected function executeCreateActivity()
    {
        $this->activityType->expects($this->any())
                ->method("belongsToProgram")
                ->willReturn(true);
        return $this->program->createActivity($this->activityId, $this->activityType, $this->activityDataProvider);
    }
    public function test_createActivity_returnActivity()
    {
        $this->assertInstanceOf(Activity::class, $this->executeCreateActivity());
    }
    public function test_createActivity_activityTypeDoesntBelongsToProgram_forbidden()
    {
        $this->activityType->expects($this->once())
                ->method("belongsToProgram")
                ->with($this->program)
                ->willReturn(false);
        $operation = function (){
            $this->executeCreateActivity();
        };
        $errorDetail = "forbidden: activity type belongs to different program";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    public function test_firmIdEquals_sameFirmId_returnTrue()
    {
        $this->assertTrue($this->program->firmIdEquals($this->program->firmId));
    }
    public function test_firmIdEquals_differentFirmId_returnFalse()
    {
        $this->assertFalse($this->program->firmIdEquals("differentFirmId"));
    }
}

class TestableProgram extends Program
{
    public $firmId = "firmId";
    public $id;
    public $removed = false;
    
    function __construct()
    {
        parent::__construct();
    }
}
