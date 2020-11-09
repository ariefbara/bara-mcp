<?php

namespace ActivityCreator\Domain\DependencyModel\Firm\Personnel;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Program,
    DependencyModel\Firm\Program\ActivityType,
    Model\Activity,
    Model\ConsultantActivity
};
use SharedContext\Domain\ValueObject\ActivityParticipantType;
use Tests\TestBase;

class ConsultantTest extends TestBase
{
    protected $program;
    protected $consultant;
    protected $activityId = "activityId", $activityType, $activityDataProvider = "string represent data provider";


    protected function setUp(): void
    {
        parent::setUp();
        $this->consultant = new TestableConsultant();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->consultant->program = $this->program;
        
        $this->activityType = $this->buildMockOfClass(ActivityType::class);
    }
    
    public function test_belongsToProgram_sameProgram_returnTrue()
    {
        $this->assertTrue($this->consultant->belongsToProgram($this->consultant->program));
    }
    public function test_belongsToProgram_differentProgram_returnFalse()
    {
        $program = $this->buildMockOfClass(Program::class);
        $this->assertFalse($this->consultant->belongsToProgram($program));
    }
    
    protected function executeInitiateActivity()
    {
        $this->activityType->expects($this->any())
                ->method("canBeInitiatedBy")
                ->willReturn(true);
        
        return $this->consultant->initiateActivity($this->activityId, $this->activityType, $this->activityDataProvider);
    }
    public function test_initiateActivity_returnConsultantActivity()
    {
        $activity = $this->buildMockOfClass(Activity::class);
        $this->program->expects($this->once())
                ->method("createActivity")
                ->with($this->activityId, $this->activityType, $this->activityDataProvider)
                ->willReturn($activity);
        
        $consultantActivity = new ConsultantActivity($this->consultant, $this->activityId, $activity);
        $this->assertEquals($consultantActivity, $this->executeInitiateActivity());
    }
    public function test_initiateActivity_activityCannotBeInitiatedByConsultant_forbidden()
    {
        $this->activityType->expects($this->once())
                ->method("canBeInitiatedBy")
                ->with(new ActivityParticipantType(ActivityParticipantType::CONSULTANT))
                ->willReturn(false);
        $operation = function (){
            $this->executeInitiateActivity();
        };
        $errorDetail = "forbidden: consultant not allowed to initiate this activity";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
}

class TestableConsultant extends Consultant
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
