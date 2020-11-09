<?php

namespace ActivityCreator\Domain\DependencyModel\Firm\Program\ActivityType;

use SharedContext\Domain\ValueObject\ {
    ActivityParticipantPriviledge,
    ActivityParticipantType
};
use Tests\TestBase;

class ActivityParticipantTest extends TestBase
{
    protected $activityParticipant;
    protected $activityParticipantType;
    protected $priviledge;


    protected function setUp(): void
    {
        parent::setUp();
        $this->activityParticipant = new TestableActivityParticipant();
        
        $this->activityParticipantType = $this->buildMockOfClass(ActivityParticipantType::class);
        $this->activityParticipant->participantType = $this->activityParticipantType;
        
        $this->priviledge = $this->buildMockOfClass(ActivityParticipantPriviledge::class);
        $this->activityParticipant->participantPriviledge = $this->priviledge;
    }
    
    protected function executeCanInitiateAndTypeEquals()
    {
        $this->activityParticipantType->expects($this->any())
                ->method("sameValueAs")
                ->willReturn(true);
        $this->priviledge->expects($this->any())
                ->method("canInitiate")
                ->willReturn(true);
        return $this->activityParticipant->canInitiateAndTypeEquals($this->activityParticipantType);
    }
    public function test_canInitiateAndTypeEquals_sameTypeAndHasCanInitiatePriviledge_returnTrue()
    {
        $this->assertTrue($this->executeCanInitiateAndTypeEquals());
    }
    public function test_canInitiateAndTypeEquals_differentType_returnFalse()
    {
        $this->activityParticipantType->expects($this->once())
                ->method("sameValueAs")
                ->with($this->activityParticipantType)
                ->willReturn(false);
        $this->assertFalse($this->executeCanInitiateAndTypeEquals());
    }
    public function test_canInitiateAndTypeEquals_cantInitiate_returnFalse()
    {
        $this->priviledge->expects($this->once())
                ->method("canInitiate")
                ->willReturn(false);
        $this->assertFalse($this->executeCanInitiateAndTypeEquals());
    }
    
    protected function executeCanAttendAndTypeEquals()
    {
        $this->activityParticipantType->expects($this->any())
                ->method("sameValueAs")
                ->willReturn(true);
        $this->priviledge->expects($this->any())
                ->method("canAttend")
                ->willReturn(true);
        return $this->activityParticipant->canAttendAndTypeEquals($this->activityParticipantType);
    }
    public function test_canAttendAndTypeEquals_sameTypeAndHasCanAttendPriviledge_returnTrue()
    {
        $this->assertTrue($this->executeCanAttendAndTypeEquals());
    }
    public function test_canAttendAndTypeEquals_differentType_returnFalse()
    {
        $this->activityParticipantType->expects($this->once())
                ->method("sameValueAs")
                ->with($this->activityParticipantType)
                ->willReturn(false);
        $this->assertFalse($this->executeCanAttendAndTypeEquals());
    }
    public function test_canAttendAndTypeEquals_cantAttend_returnFalse()
    {
        $this->priviledge->expects($this->once())
                ->method("canAttend")
                ->willReturn(false);
        $this->assertFalse($this->executeCanAttendAndTypeEquals());
    }
}

class TestableActivityParticipant extends ActivityParticipant
{
    public $activityType;
    public $id;
    public $participantType;
    public $participantPriviledge;
    
    function __construct()
    {
        parent::__construct();
    }
}
