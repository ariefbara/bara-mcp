<?php

namespace ActivityCreator\Domain\DependencyModel\Firm\Program;

use ActivityCreator\Domain\DependencyModel\Firm\ {
    Program,
    Program\ActivityType\ActivityParticipant
};
use Doctrine\Common\Collections\ArrayCollection;
use SharedContext\Domain\ValueObject\ActivityParticipantType;
use Tests\TestBase;

class ActivityTypeTest extends TestBase
{
    protected $activityType;
    protected $activityParticipant;
    protected $activityParticipantType;

    protected function setUp(): void
    {
        parent::setUp();
        $this->activityType = new TestableActivityType();
        $this->activityType->participants = new ArrayCollection();
        $this->activityType->program = $this->buildMockOfClass(Program::class);
        
        $this->activityParticipant = $this->buildMockOfClass(ActivityParticipant::class);
        $this->activityType->participants->add($this->activityParticipant);
        
        $this->activityParticipantType = $this->buildMockOfClass(ActivityParticipantType::class);
    }
    
    protected function executeCanBeInititatedBy()
    {
        $this->activityParticipant->expects($this->any())
                ->method("canInitiateAndTypeEquals")
                ->willReturn(true);
        return $this->activityType->canBeInitiatedBy($this->activityParticipantType);
    }
    public function test_canBeInitiatedBy_containActivityParticipantWithSameTypeAndCanInitiate_returnTrue()
    {
        $this->assertTrue($this->executeCanBeInititatedBy());
    }
    public function test_canBeInititaedBy_noActivityParticipantWithEqualsTypeAbleToInititae_returnFalse()
    {
        $this->activityParticipant->expects($this->once())
                ->method("canInitiateAndTypeEquals")
                ->with($this->activityParticipantType)
                ->willReturn(false);
        $this->assertFalse($this->executeCanBeInititatedBy());
    }
    
    public function test_belongsToProgram_sameProgram_returnTrue()
    {
        $this->assertTrue($this->activityType->belongsToProgram($this->activityType->program));
    }
    public function test_belongsToProgram_differentProgram_returnFalse()
    {
        $program = $this->buildMockOfClass(Program::class);
        $this->assertFalse($this->activityType->belongsToProgram($program));
    }
    
    protected function executeCanInvite()
    {
        $this->activityParticipant->expects($this->any())
                ->method("canAttendAndTypeEquals")
                ->willReturn(true);
        return $this->activityType->canInvite($this->activityParticipantType);
    }
    public function test_canInvite_containActivityParticipantWithSameTypeAndCanBeInvited_returnTrue()
    {
        $this->assertTrue($this->executeCanInvite());
    }
    public function test_canInvite_noActivityParticipantWithEqualsTypeCanBeInvited_returnFalse()
    {
        $this->activityParticipant->expects($this->once())
                ->method("canAttendAndTypeEquals")
                ->with($this->activityParticipantType)
                ->willReturn(false);
        $this->assertFalse($this->executeCanInvite());
    }
    
}

class TestableActivityType extends ActivityType
{
    public $program;
    public $id;
    public $participants;
    
    function __construct()
    {
        parent::__construct();
    }
}
