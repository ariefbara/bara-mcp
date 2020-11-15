<?php

namespace ActivityCreator\Domain\DependencyModel\Firm\Program\ActivityType;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Manager,
    DependencyModel\Firm\Personnel\Consultant,
    DependencyModel\Firm\Personnel\Coordinator,
    DependencyModel\Firm\Program\Participant,
    Model\Activity,
    service\ActivityDataProvider
};
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
    protected $activity;
    protected $activityDataProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->activityParticipant = new TestableActivityParticipant();
        
        $this->activityParticipantType = $this->buildMockOfClass(ActivityParticipantType::class);
        $this->activityParticipant->participantType = $this->activityParticipantType;
        
        $this->priviledge = $this->buildMockOfClass(ActivityParticipantPriviledge::class);
        $this->activityParticipant->participantPriviledge = $this->priviledge;
        
        $this->activity = $this->buildMockOfClass(Activity::class);
        $this->activityDataProvider = $this->buildMockOfClass(ActivityDataProvider::class);
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
    
    protected function executeAddInviteesToActivity()
    {
        $this->priviledge->expects($this->any())
                ->method("canAttend")
                ->willReturn(true);
        $this->activityParticipant->addInviteesToActivity($this->activity, $this->activityDataProvider);
    }
    public function test_addInviteesToActivity_aCoordinatorTypeParticipant_inviteAllCoordinatorInDataProviderToActivity()
    {
        $this->activityParticipantType->expects($this->once())
                ->method("isCoordinatorType")
                ->willReturn(true);
        
        $coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->activityDataProvider->expects($this->once())
                ->method("iterateInvitedCoordinatorList")
                ->willReturn([$coordinator]);
        
        $this->activity->expects($this->once())
                ->method("addInvitee")
                ->with($coordinator, $this->activityParticipant);
        
        $this->executeAddInviteesToActivity();
    }
    public function test_addInviteesToActivity_aConsultantTypeParticipant_inviteAllConsultantInDataProviderToActivity()
    {
        $this->activityParticipantType->expects($this->once())
                ->method("isConsultantType")
                ->willReturn(true);
        
        $consultant = $this->buildMockOfClass(Consultant::class);
        $this->activityDataProvider->expects($this->once())
                ->method("iterateInvitedConsultantList")
                ->willReturn([$consultant]);
        
        $this->activity->expects($this->once())
                ->method("addInvitee")
                ->with($consultant, $this->activityParticipant);
        
        $this->executeAddInviteesToActivity();
    }
    public function test_addInviteesToActivity_aManagerTypeParticipant_inviteAllManagerInDataProviderToActivity()
    {
        $this->activityParticipantType->expects($this->once())
                ->method("isManagerType")
                ->willReturn(true);
        
        $manager = $this->buildMockOfClass(Manager::class);
        $this->activityDataProvider->expects($this->once())
                ->method("iterateInvitedManagerList")
                ->willReturn([$manager]);
        
        $this->activity->expects($this->once())
                ->method("addInvitee")
                ->with($manager, $this->activityParticipant);
        
        $this->executeAddInviteesToActivity();
    }
    public function test_addInviteesToActivity_aParticipantTypeParticipant_inviteAllParticipantInDataProviderToActivity()
    {
        $this->activityParticipantType->expects($this->once())
                ->method("isParticipantType")
                ->willReturn(true);
        
        $participant = $this->buildMockOfClass(Participant::class);
        $this->activityDataProvider->expects($this->once())
                ->method("iterateInvitedParticipantList")
                ->willReturn([$participant]);
        
        $this->activity->expects($this->once())
                ->method("addInvitee")
                ->with($participant, $this->activityParticipant);
        
        $this->executeAddInviteesToActivity();
    }
    public function test_addInviteesToActivity_cannotAttend_preventAddInvitee()
    {
        $this->priviledge->expects($this->once())
                ->method("canAttend")
                ->willReturn(false);
        $this->activityParticipantType->expects($this->never())
                ->method("isCoordinatorType");
        $this->executeAddInviteesToActivity();
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
