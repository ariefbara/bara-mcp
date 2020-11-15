<?php

namespace ActivityCreator\Domain\Model\Activity;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Manager,
    DependencyModel\Firm\Personnel\Consultant,
    DependencyModel\Firm\Personnel\Coordinator,
    DependencyModel\Firm\Program\ActivityType\ActivityParticipant,
    DependencyModel\Firm\Program\Participant,
    Model\Activity,
    Model\Activity\Invitee\ConsultantInvitee,
    Model\Activity\Invitee\CoordinatorInvitee,
    Model\Activity\Invitee\ManagerInvitee,
    Model\Activity\Invitee\ParticipantInvitee,
    Model\CanReceiveInvitation
};
use Tests\TestBase;

class InviteeTest extends TestBase
{
    protected $activity;
    protected $activityParticipant;
    protected $recipient;
    protected $invitee;
    protected $managerInvitation;
    protected $coordinatorInvitation;
    protected $consultantInvitation;
    protected $participantInvitation;
    protected $id = "newId";
    protected $manager;
    protected $coordinator;
    protected $consultant;
    protected $participant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->activity = $this->buildMockOfClass(Activity::class); 
        $this->activityParticipant = $this->buildMockOfClass(ActivityParticipant::class);
        $this->recipient = $this->buildMockOfInterface(CanReceiveInvitation::class);
        
        $this->invitee = new TestableInvitee($this->activity, "id", $this->activityParticipant, $this->recipient);
        $this->managerInvitation = $this->buildMockOfClass(ManagerInvitee::class);
        $this->invitee->managerInvitee = $this->managerInvitation;
        
        $this->coordinatorInvitation = $this->buildMockOfClass(CoordinatorInvitee::class);
        $this->consultantInvitation = $this->buildMockOfClass(ConsultantInvitee::class);
        $this->participantInvitation = $this->buildMockOfClass(ParticipantInvitee::class);
        
        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->participant = $this->buildMockOfClass(Participant::class);
    }
    
    protected function executeConstruct()
    {
        return new TestableInvitee($this->activity, $this->id, $this->activityParticipant, $this->recipient);
    }
    
    public function test_construct_setProperties()
    {
        $invitee = $this->executeConstruct();
        $this->assertEquals($this->activity, $invitee->activity);
        $this->assertEquals($this->id, $invitee->id);
        $this->assertEquals($this->activityParticipant, $invitee->activityParticipant);
        $this->assertNull($invitee->willAttend);
        $this->assertNull($invitee->attended);
        $this->assertFalse($invitee->invitationCancelled);
    }
    public function test_construct_registerReciepientAsInvitationRecipient()
    {
        $this->recipient->expects($this->once())
                ->method("registerAsInviteeRecipient");
        $this->executeConstruct();
    }
    
    public function test_cancelInvitation_setInvitationCacelledTrue()
    {
        $this->invitee->cancelInvitation();
        $this->assertTrue($this->invitee->invitationCancelled);
    }
    
    public function test_reinvite_setCancelledFalse()
    {
        $this->invitee->invitationCancelled = true;
        
        $this->invitee->reinvite();
        $this->assertFalse($this->invitee->invitationCancelled);
    }
    
    public function test_correspondWithRecipient_aManagerInvitation_returnManagerInvitationManagerEqualsResult()
    {
        $this->managerInvitation->expects($this->once())
                ->method("managerEquals")
                ->with($this->manager);
        $this->invitee->correspondWithRecipient($this->manager);
    }
    public function test_correspondWithRecipient_aCoordinatorInvitation_returnCoordinatorInvitationCoordinatorEqualsResult()
    {
        $this->invitee->managerInvitee = null;
        $this->invitee->coordinatorInvitee = $this->coordinatorInvitation;
        
        $this->coordinatorInvitation->expects($this->once())
                ->method("coordinatorEquals")
                ->with($this->coordinator);
        $this->invitee->correspondWithRecipient($this->coordinator);
    }
    public function test_correspondWithRecipient_aConsultantInvitation_returnConsultantInvitationConsultantEqualsResult()
    {
        $this->invitee->managerInvitee = null;
        $this->invitee->consultantInvitee = $this->consultantInvitation;
        
        $this->consultantInvitation->expects($this->once())
                ->method("consultantEquals")
                ->with($this->consultant);
        $this->invitee->correspondWithRecipient($this->consultant);
    }
    public function test_correspondWithRecipient_aParticipantInvitation_returnParticipantInvitationParticipantEqualsResult()
    {
        $this->invitee->managerInvitee = null;
        $this->invitee->participantInvitee = $this->participantInvitation;
        
        $this->participantInvitation->expects($this->once())
                ->method("participantEquals")
                ->with($this->participant);
        $this->invitee->correspondWithRecipient($this->participant);
    }
    
    public function test_registerManagerAsRecipient_setCoordinatorInvitation()
    {
        $this->invitee->managerInvitee = null;
        
        $this->invitee->registerManagerAsRecipient($this->manager);
        $managerInvitation = new ManagerInvitee($this->invitee, $this->invitee->id, $this->manager);
        $this->assertEquals($managerInvitation, $this->invitee->managerInvitee);
    }
    
    public function test_registerCoordinatorAsRecipient_setCoordinatorInvitation()
    {
        $this->invitee->managerInvitee = null;
        
        $this->invitee->registerCoordinatorAsRecipient($this->coordinator);
        $coordinatorInvitation = new CoordinatorInvitee($this->invitee, $this->invitee->id, $this->coordinator);
        $this->assertEquals($coordinatorInvitation, $this->invitee->coordinatorInvitee);
    }
    
    public function test_registerConsultantAsRecipient_setConsultantInvitation()
    {
        $this->invitee->managerInvitee = null;
        
        $this->invitee->registerConsultantAsRecipient($this->consultant);
        $consultantInvitation = new ConsultantInvitee($this->invitee, $this->invitee->id, $this->consultant);
        $this->assertEquals($consultantInvitation, $this->invitee->consultantInvitee);
    }
    
    public function test_registerParticipantAsRecipient_setParticipantInvitation()
    {
        $this->invitee->managerInvitee = null;
        
        $this->invitee->registerParticipantAsRecipient($this->participant);
        $participantInvitation = new ParticipantInvitee($this->invitee, $this->invitee->id, $this->participant);
        $this->assertEquals($participantInvitation, $this->invitee->participantInvitee);
    }
}

class TestableInvitee extends Invitee
{
    public $activity;
    public $id;
    public $activityParticipant;
    public $willAttend;
    public $attended;
    public $invitationCancelled;
    public $managerInvitee;
    public $coordinatorInvitee;
    public $consultantInvitee;
    public $participantInvitee;
}
