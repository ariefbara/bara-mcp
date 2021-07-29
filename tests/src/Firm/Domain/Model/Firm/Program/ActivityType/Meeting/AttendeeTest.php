<?php

namespace Firm\Domain\Model\Firm\Program\ActivityType\Meeting;

use Config\EventList;
use Firm\Domain\Model\Firm\Program\ActivityType\ActivityParticipant;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;
use Resources\Domain\Event\CommonEvent;
use Tests\TestBase;

class AttendeeTest extends TestBase
{
    protected $meeting;
    protected $attendeeSetup;
    protected $attendee;
    protected $id = "newId", $anInitiator = false;
    
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->meeting = $this->buildMockOfClass(Meeting::class);
        $this->attendeeSetup = $this->buildMockOfClass(ActivityParticipant::class);
        $this->attendee = new TestableAttendee($this->meeting, "id", $this->attendeeSetup, true);
        $this->attendee->recordedEvents = [];
        
        $this->task = $this->buildMockOfInterface(ITaskExecutableByMeetingInitiator::class);
    }
    
    protected function executeConstruct()
    {
        return new TestableAttendee($this->meeting, $this->id, $this->attendeeSetup, $this->anInitiator);
    }
    public function test_construct_setProperties()
    {
        $attendee = $this->executeConstruct();
        $this->assertEquals($this->meeting, $attendee->meeting);
        $this->assertEquals($this->id, $attendee->id);
        $this->assertEquals($this->attendeeSetup, $attendee->attendeeSetup);
        $this->assertEquals($this->anInitiator, $attendee->anInitiator);
        $this->assertFalse($attendee->willAttend);
        $this->assertFalse($attendee->attended);
        $this->assertFalse($attendee->cancelled);
    }
    public function test_construct_anInitiator_setwillAttendTrue()
    {
        $this->anInitiator = true;
        $attendee = $this->executeConstruct();
        $this->assertTrue($attendee->willAttend);
    }
    public function test_construct_recordMeetingInvitationSentEvent()
    {
        $attendee = $this->executeConstruct();
        $event = new CommonEvent(EventList::MEETING_INVITATION_SENT, $this->id);
        $this->assertEquals($event, $attendee->recordedEvents[0]);
    }
    public function test_construct_assertAttendeeSetupHasAttendPriviledge()
    {
        $this->attendeeSetup->expects($this->once())
                ->method('assertCanAttend');
        $this->attendeeSetup->expects($this->never())
                ->method('assertCanInitiate');
        $this->executeConstruct();
    }
    public function test_construct_anInitiator_assertAttendeeSetupHasInitiatePriviledge()
    {
        $this->anInitiator = true;
        $this->attendeeSetup->expects($this->once())
                ->method('assertCanInitiate');
        $this->attendeeSetup->expects($this->never())
                ->method('assertCanAttend');
        $this->executeConstruct();
    }
    
    protected function executeAssertActiveInitiator()
    {
        $this->attendee->assertActiveInitiator();
    }
    public function test_assertActiveInitiator_activeInitiator_void()
    {
        $this->executeAssertActiveInitiator();
        $this->markAsSuccess();
    }
    public function test_assertActiveInitiator_inactiveAttendee_forbidden()
    {
        $this->attendee->cancelled = true;
        $this->assertRegularExceptionThrowed(function (){
            $this->executeAssertActiveInitiator();
        }, 'Forbidden', 'forbidden: not an active meeting initiator');
    }
    public function test_assertActiveInitiator_notAnInitiator_forbidden()
    {
        $this->attendee->anInitiator = false;
        $this->assertRegularExceptionThrowed(function (){
            $this->executeAssertActiveInitiator();
        }, 'Forbidden', 'forbidden: not an active meeting initiator');
    }
    
    protected function executeCancel()
    {
        $this->attendee->cancel();
    }
    public function test_cancel_setCancelledTrue()
    {
        $this->attendee->anInitiator = false;
        $this->executeCancel();
        $this->assertTrue($this->attendee->cancelled);
    }
    public function test_cancel_anInitiator_forbidden()
    {
        $this->attendee->anInitiator = true;
        $operation = function (){
            $this->executeCancel();
        };
        $errorDetail = "forbidden: cannot cancel invitationt to initiator";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_cancel_recordMeetingInvitationCancelledEvent()
    {
        $this->attendee->anInitiator = false;
        $this->executeCancel();
        $event = new CommonEvent(EventList::MEETING_INVITATION_CANCELLED, $this->attendee->id);
        $this->assertEquals($event, $this->attendee->recordedEvents[0]);
    }
    
    protected function executeIsActiveAttendeeOfMeeting()
    {
        return $this->attendee->isActiveAttendeeOfMeeting($this->meeting);
    }
    public function test_isActiveAttendeeOfMeeting_activeAttendeeOfSameMeeting_returnTrue()
    {
        $this->assertTrue($this->executeIsActiveAttendeeOfMeeting());
    }
    public function test_isActiveAttendeeOfMeeting_inactiveAttendee_returnFalse()
    {
        $this->attendee->cancelled = true;
        $this->assertFalse($this->executeIsActiveAttendeeOfMeeting());
    }
    public function test_isActiveAttendeeOfMeeting_differentMeeting_returnFalse()
    {
        $this->attendee->meeting = $this->buildMockOfClass(Meeting::class);
        $this->assertFalse($this->executeIsActiveAttendeeOfMeeting());
    }
    
    protected function executeDisableValidInvitation()
    {
        $this->meeting->expects($this->any())
                ->method('isUpcoming')
                ->willReturn(true);
        $this->attendee->disableValidInvitation();
    }
    public function test_disabelValidInvitation_upcomingMeeting_cancelledInvitation()
    {
        $this->executeDisableValidInvitation();
        $this->assertTrue($this->attendee->cancelled);
    }
    public function test_disableValidInvitation_notAnUpcomingMeeting_void()
    {
        $this->meeting->expects($this->any())
                ->method('isUpcoming')
                ->willReturn(false);
        $this->executeDisableValidInvitation();
        $this->assertFalse($this->attendee->cancelled);
    }
    
    protected function executeTaskAsMeetingInitiator()
    {
        $this->attendee->executeTaskAsMeetingInitiator($this->task);
    }
    public function test_executeTaskAsMeetingInitiator_executeTask()
    {
        $this->task->expects($this->once())
                ->method('executeByMeetingInitiatorOf')
                ->with($this->meeting);
        $this->executeTaskAsMeetingInitiator();
    }
    public function test_executeTaskAsMeetingInitiator_inactiveAttendee_forbidden()
    {
        $this->attendee->cancelled = true;
        $this->assertRegularExceptionThrowed(function (){
            $this->executeTaskAsMeetingInitiator();
        }, 'Forbidden', 'forbidden: not an active meeting initiator');
    }
    public function test_executeTaskAsMeetingInitiator_notAnInitiator_forbidden()
    {
        $this->attendee->anInitiator = false;
        $this->assertRegularExceptionThrowed(function (){
            $this->executeTaskAsMeetingInitiator();
        }, 'Forbidden', 'forbidden: not an active meeting initiator');
    }
    
    protected function executeAssertManageableInMeeting()
    {
        $this->attendee->assertManageableInMeeting($this->meeting);
    }
    public function test_assertManageableInMeeting_activeAttendeeFromSameMeeting_void()
    {
        $this->executeAssertManageableInMeeting();
        $this->markAsSuccess();
    }
    public function test_assertManageableInMeeting_inactiveAttendee_forbidden()
    {
        $this->attendee->cancelled = true;
        $this->assertRegularExceptionThrowed(function (){
            $this->executeAssertManageableInMeeting();
        }, 'Forbidden', "forbidden: unamanged attendee");
    }
    public function test_assertManageableInMeeting_differentMeeting_forbidden()
    {
        $this->attendee->meeting = $this->buildMockOfClass(Meeting::class);
        $this->assertRegularExceptionThrowed(function (){
            $this->executeAssertManageableInMeeting();
        }, 'Forbidden', "forbidden: unamanged attendee");
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
    public $recordedEvents;
}
