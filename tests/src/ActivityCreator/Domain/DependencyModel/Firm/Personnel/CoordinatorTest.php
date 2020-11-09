<?php

namespace ActivityCreator\Domain\DependencyModel\Firm\Personnel;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Program,
    DependencyModel\Firm\Program\ActivityType,
    Model\CoordinatorActivity
};
use SharedContext\Domain\ValueObject\ActivityParticipantType;
use Tests\TestBase;

class CoordinatorTest extends TestBase
{
    protected $program;
    protected $coordinator;
    
    protected $coordinatorActivityId = "activityId", $activityType, $activityDataProvider = "activityDataProvider";

    protected function setUp(): void
    {
        parent::setUp();
        $this->coordinator = new TestableCoordinator();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->coordinator->program = $this->program;
        
        $this->activityType = $this->buildMockOfClass(ActivityType::class);
    }
    
    protected function executeInitiateActivity()
    {
        $this->activityType->expects($this->any())
                ->method("canBeInitiatedBy")
                ->willReturn(true);
        return $this->coordinator->initiateActivity($this->coordinatorActivityId, $this->activityType, $this->activityDataProvider);
    }
    public function test_initiateActivity_returnCoordinatorActivity()
    {
        $this->program->expects($this->once())
                ->method("createActivity")
                ->with($this->coordinatorActivityId, $this->activityType, $this->activityDataProvider);
        $this->assertInstanceOf(CoordinatorActivity::class, $this->executeInitiateActivity());
    }
    public function test_initiateActivity_activityCannotBeInitiatedByCoordinator_forbidden()
    {
        $this->activityType->expects($this->once())
                ->method("canBeInitiatedBy")
                ->with(new ActivityParticipantType(ActivityParticipantType::COORDINATOR))
                ->willReturn(false);
        $operation = function (){
            $this->executeInitiateActivity();
        };
        $errorDetail = "forbidden: coordinator not allowed to initiate this activity";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    public function test_belongsToProgram_sameProgram_returnTrue()
    {
        $this->assertTrue($this->coordinator->belongsToProgram($this->coordinator->program));
    }
    public function test_belongsToProgram_differentProgram_returnFalse()
    {
        $program = $this->buildMockOfClass(Program::class);
        $this->assertFalse($this->coordinator->belongsToProgram($program));
    }
}

class TestableCoordinator extends Coordinator
{
    public $personnel;
    public $id;
    public $program;
    public $removed;
    
    function __construct()
    {
        parent::__construct();
    }
}
