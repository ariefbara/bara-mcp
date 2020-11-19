<?php

namespace Firm\Domain\Model\Firm\Program\MeetingType\Meeting;

use Firm\Domain\Model\Firm\ {
    Manager,
    Program\ActivityType\ActivityParticipant,
    Program\Consultant,
    Program\Coordinator,
    Program\MeetingType\CanAttendMeeting,
    Program\MeetingType\Meeting,
    Program\MeetingType\Meeting\Attendee\ConsultantAttendee,
    Program\MeetingType\Meeting\Attendee\CoordinatorAttendee,
    Program\MeetingType\Meeting\Attendee\ManagerAttendee,
    Program\MeetingType\Meeting\Attendee\ParticipantAttendee,
    Program\MeetingType\MeetingData,
    Program\Participant
};
use Tests\TestBase;

class AttendeeTest extends TestBase
{
    protected $meeting;
    protected $attendeeSetup;
    protected $user;
    protected $attendee;
    protected $managerAttendee;
    protected $coordinatorAttendee;
    protected $consultantAttendee;
    protected $participantAttendee;
    protected $id = "newId", $anInitiator = false;
    protected $meetingData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->meeting = $this->buildMockOfClass(Meeting::class);
        $this->attendeeSetup = $this->buildMockOfClass(ActivityParticipant::class);
        $this->user = $this->buildMockOfInterface(CanAttendMeeting::class);
        $this->attendee = new TestableAttendee($this->meeting, "id", $this->attendeeSetup, $this->user);
        
        $this->managerAttendee = $this->buildMockOfClass(ManagerAttendee::class);
        $this->coordinatorAttendee = $this->buildMockOfClass(CoordinatorAttendee::class);
        $this->consultantAttendee = $this->buildMockOfClass(ConsultantAttendee::class);
        $this->participantAttendee = $this->buildMockOfClass(ParticipantAttendee::class);
        
        $this->meetingData = $this->buildMockOfClass(MeetingData::class);
    }
    
    protected function executeConstruct()
    {
        return new TestableAttendee($this->meeting, $this->id, $this->attendeeSetup, $this->user, $this->anInitiator);
    }
    public function test_construct_setProperties()
    {
        $attendee = $this->executeConstruct();
        $this->assertEquals($this->meeting, $attendee->meeting);
        $this->assertEquals($this->id, $attendee->id);
        $this->assertEquals($this->attendeeSetup, $attendee->attendeeSetup);
        $this->assertEquals($this->anInitiator, $attendee->anInitiator);
        $this->assertNull($attendee->willAttend);
        $this->assertNull($attendee->attended);
        $this->assertFalse($attendee->cancelled);
    }
    public function test_construct_anInitiator_setwillAttendTrue()
    {
        $this->anInitiator = true;
        $attendee = $this->executeConstruct();
        $this->assertTrue($attendee->willAttend);
    }
    public function test_construct_setUserAsAttendeeCandidate()
    {
        $this->user->expects($this->once())
                ->method("registerAsAttendeeCandidate");
        $this->executeConstruct();
    }
    
    protected function executeUpdateMeeting()
    {
        $this->attendee->updateMeeting($this->meetingData);
    }
    public function test_updateMeeting_updateMeeting()
    {
        $this->attendee->anInitiator = true;
        $this->meeting->expects($this->once())
                ->method("update")
                ->with($this->meetingData);
        $this->executeUpdateMeeting();
    }
    public function test_updateMeeting_attendeeNotAnInitiator_forbidden()
    {
        $this->attendee->anInitiator = false;
        $operation = function (){
            $this->executeUpdateMeeting();
        };
        $errorDetail = "forbidden: only meeting initiator can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    public function test_cancel_setCancelledTrue()
    {
        $this->attendee->cancel();
        $this->assertTrue($this->attendee->cancelled);
    }
    
    public function test_reinvite_setCancelledFalse()
    {
        $this->attendee->cancelled = true;
        $this->attendee->reinvite();
        $this->assertFalse($this->attendee->cancelled);
    }
    
    public function test_correspondWithUser_aManagerAttendee_returnManagerAttendeesManagerEqualsResult()
    {
        $this->attendee->managerAttendee = $this->managerAttendee;
        $this->managerAttendee->expects($this->once())
                ->method("managerEquals")
                ->with($this->user);
        $this->attendee->correspondWithUser($this->user);
    }
    public function test_correspondWithUser_aCoordinatorAttendee_returnCoordinatorAttendeesCoordinatorEqualsResult()
    {
        $this->attendee->coordinatorAttendee = $this->coordinatorAttendee;
        $this->coordinatorAttendee->expects($this->once())
                ->method("coordinatorEquals")
                ->with($this->user);
        $this->attendee->correspondWithUser($this->user);
    }
    public function test_correspondWithUser_aConsultantAttendee_returnConsultantAttendeesConsultantEqualsResult()
    {
        $this->attendee->consultantAttendee = $this->consultantAttendee;
        $this->consultantAttendee->expects($this->once())
                ->method("consultantEquals")
                ->with($this->user);
        $this->attendee->correspondWithUser($this->user);
    }
    public function test_correspondWithUser_aParticipantAttendee_returnParticipantAttendeesParticipantEqualsResult()
    {
        $this->attendee->participantAttendee = $this->participantAttendee;
        $this->participantAttendee->expects($this->once())
                ->method("participantEquals")
                ->with($this->user);
        $this->attendee->correspondWithUser($this->user);
    }
    
    public function test_setManagerAsAttendeeCandidate_setManagerAttendee()
    {
        $manager = $this->buildMockOfClass(Manager::class);
        $this->attendee->setManagerAsAttendeeCandidate($manager);
        $managerAttendee = new ManagerAttendee($this->attendee, $this->attendee->id, $manager);
        $this->assertEquals($managerAttendee, $this->attendee->managerAttendee);
    }
    
    public function test_setCoordinatorAsAttendeeCandidate_setCoordinatorAttendee()
    {
        $coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->attendee->setCoordinatorAsAttendeeCandidate($coordinator);
        $coordinatorAttendee = new CoordinatorAttendee($this->attendee, $this->attendee->id, $coordinator);
        $this->assertEquals($coordinatorAttendee, $this->attendee->coordinatorAttendee);
    }
    
    public function test_setConsultantAsAttendeeCandidate_setConsultantAttendee()
    {
        $consultant = $this->buildMockOfClass(Consultant::class);
        $this->attendee->setConsultantAsAttendeeCandidate($consultant);
        $consultantAttendee = new ConsultantAttendee($this->attendee, $this->attendee->id, $consultant);
        $this->assertEquals($consultantAttendee, $this->attendee->consultantAttendee);
    }
    
    public function test_setParticipantAsAttendeeCandidate_setParticipantAttendee()
    {
        $participant = $this->buildMockOfClass(Participant::class);
        $this->attendee->setParticipantAsAttendeeCandidate($participant);
        $participantAttendee = new ParticipantAttendee($this->attendee, $this->attendee->id, $participant);
        $this->assertEquals($participantAttendee, $this->attendee->participantAttendee);
    }
}

class TestableAttendee extends Attendee
{
    public $meeting;
    public $id;
    public $attendeeSetup;
    public $willAttend;
    public $attended;
    public $anInitiator;
    public $cancelled;
    public $managerAttendee;
    public $coordinatorAttendee;
    public $consultantAttendee;
    public $participantAttendee;
}
