<?php

namespace Firm\Domain\Model\Firm\Program\ActivityType;

use Config\EventList;
use DateTimeImmutable;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\ActivityType;
use Firm\Domain\Model\Firm\Program\ActivityType\ActivityParticipant;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\Attendee;
use Firm\Domain\Model\Firm\Program\CanAttendMeeting;
use Resources\DateTimeImmutableBuilder;
use Resources\Domain\Event\CommonEvent;
use Resources\Domain\ValueObject\DateTimeInterval;
use SharedContext\Domain\ValueObject\ActivityParticipantType;
use Tests\TestBase;

class MeetingTest extends TestBase
{
    protected $meetingType;
    protected $meeting;
    protected $startEndTime;
    protected $id = "newId", $name = "new name", $description = "new description ", $startTime, $endTime,
            $location = "new location", $note = "new note";
    
    protected $attendeeId = 'attendee-id', $activityParticipantType, $anInitiator = true;
    protected $user;
    protected $program;
    protected $firm;

    protected function setUp(): void
    {
        parent::setUp();
        $this->meetingType = $this->buildMockOfClass(ActivityType::class);
        $meetingData = new MeetingData(
                "name", "description", new DateTimeImmutable("+24 hours"), new DateTimeImmutable("+25 hours"),
                "location", "note");

        $this->meeting = new TestableMeeting($this->meetingType, 'id', $meetingData);
        $this->meeting->recordedEvents = [];
        $this->startEndTime = $this->buildMockOfClass(DateTimeInterval::class);
        $this->meeting->startEndTime = $this->startEndTime;
        
        $this->startTime = new DateTimeImmutable("+48 hours");
        $this->endTime = new DateTimeImmutable("+50 hours");
        
        $this->activityParticipantType = $this->buildMockOfClass(ActivityParticipantType::class);
        
        $this->user = $this->buildMockOfInterface(CanAttendMeeting::class);
        
        $this->program = $this->buildMockOfClass(Program::class);
        $this->firm = $this->buildMockOfClass(Firm::class);
    }
    
    protected function getMeetingData()
    {
        return new MeetingData($this->name, $this->description, $this->startTime, $this->endTime, $this->location, $this->note);
    }
    
    protected function executeConstruct()
    {
        return new TestableMeeting($this->meetingType, $this->id, $this->getMeetingData());
    }
    public function test_construct_setProperties()
    {
        $meeting = $this->executeConstruct();
        $this->assertEquals($this->meetingType, $meeting->meetingType);
        $this->assertEquals($this->id, $meeting->id);
        $this->assertEquals($this->name, $meeting->name);
        $this->assertEquals($this->description, $meeting->description);

        $startEndTime = new DateTimeInterval($this->startTime, $this->endTime);
        $this->assertEquals($startEndTime, $meeting->startEndTime);

        $this->assertEquals($this->location, $meeting->location);
        $this->assertEquals($this->note, $meeting->note);
        $this->assertFalse($meeting->cancelled);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $meeting->createdTime);
    }
    public function test_construct_emptyName_badRequest()
    {
        $this->name = "";
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = "bad request: meeting name is mandatory";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    public function test_construct_nullStartTime_badRequest()
    {
        $this->startTime = null;
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = "bad request: meeting start time is mandatory";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    public function test_construct_nullEndTime_badRequest()
    {
        $this->endTime = null;
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = "bad request: meeting end time is mandatory";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    public function test_construct_recordEvent()
    {
        $meeting = $this->executeConstruct();
        $event = new CommonEvent(EventList::MEETING_CREATED, $this->id);
        $this->assertEquals($event, $meeting->recordedEvents[0]);
    }
    
    protected function executeAssertUsableInProgram()
    {
        $this->startEndTime->expects($this->any())
                ->method('isUpcoming')
                ->willReturn(true);
        $this->meetingType->expects($this->any())
                ->method('belongsToProgram')
                ->with($this->program)
                ->willReturn(true);
        $this->meeting->assertUsableInProgram($this->program);
    }
    public function test_assertUsableInProgram_upcomingMeetingWithActivityTypeFromSameProgram_void()
    {
        $this->executeAssertUsableInProgram();
        $this->markAsSuccess();
    }
    public function test_assertUsableInProgram_cancelled_forbidden()
    {
        $this->meeting->cancelled = true;
        $this->assertRegularExceptionThrowed(function () {
            $this->executeAssertUsableInProgram();
        }, 'Forbidden', 'forbidden: unuseable meeting');
    }
    public function test_assertUsableInProgram_notAnUpcomingMeeting_forbidden()
    {
        $this->startEndTime->expects($this->once())
                ->method('isUpcoming')
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function () {
            $this->executeAssertUsableInProgram();
        }, 'Forbidden', 'forbidden: unuseable meeting');
    }
    public function test_assertUsableInProgram_activityTypeDoesntBelongsToSameProgram_forbidden()
    {
        $this->meetingType->expects($this->any())
                ->method('belongsToProgram')
                ->with($this->program)
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function () {
            $this->executeAssertUsableInProgram();
        }, 'Forbidden', 'forbidden: unuseable meeting');
    }
    
    protected function executeAssertUsableInFirm()
    {
        $this->startEndTime->expects($this->any())
                ->method('isUpcoming')
                ->willReturn(true);
        $this->meetingType->expects($this->any())
                ->method('belongsToFirm')
                ->with($this->firm)
                ->willReturn(true);
        $this->meeting->assertUsableInFirm($this->firm);
    }
    public function test_assertUsableInFirm_upcomingMeetingWithActivityTypeFromSameFirm_void()
    {
        $this->executeAssertUsableInFirm();
        $this->markAsSuccess();
    }
    public function test_assertUsableInFirm_cancelled_forbidden()
    {
        $this->meeting->cancelled = true;
        $this->assertRegularExceptionThrowed(function () {
            $this->executeAssertUsableInFirm();
        }, 'Forbidden', 'forbidden: unuseable meeting');
    }
    public function test_assertUsableInFirm_notAnUpcomingMeeting_forbidden()
    {
        $this->startEndTime->expects($this->once())
                ->method('isUpcoming')
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function () {
            $this->executeAssertUsableInFirm();
        }, 'Forbidden', 'forbidden: unuseable meeting');
    }
    public function test_assertUsableInFirm_activityTypeDoesntBelongsToSameFirm_forbidden()
    {
        $this->meetingType->expects($this->any())
                ->method('belongsToFirm')
                ->with($this->firm)
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function () {
            $this->executeAssertUsableInFirm();
        }, 'Forbidden', 'forbidden: unuseable meeting');
    }
    
    public function test_isUpcoming_returnStartEndDateIsUpcomingResult()
    {
        $this->startEndTime->expects($this->once())
                ->method('isUpcoming');
        $this->meeting->isUpcoming();
    }
    
    protected function executeUpdate()
    {
        $this->meeting->update($this->getMeetingData());
    }
    public function test_update_updateProperties()
    {
        $this->executeUpdate();
        $this->assertEquals($this->name, $this->meeting->name);
        $this->assertEquals($this->description, $this->meeting->description);

        $startEndTime = new DateTimeInterval($this->startTime, $this->endTime);
        $this->assertEquals($startEndTime, $this->meeting->startEndTime);

        $this->assertEquals($this->location, $this->meeting->location);
        $this->assertEquals($this->note, $this->meeting->note);
    }
    public function test_update_recordMeetingScheduleChangedEvent()
    {
        $event = new CommonEvent(EventList::MEETING_SCHEDULE_CHANGED, $this->meeting->id);
        $this->executeUpdate();
        $this->assertEquals($event, $this->meeting->recordedEvents[0]);
    }
    public function test_update_sameSchedule_preventEventPublishing()
    {
        $this->startEndTime->expects($this->once())
                ->method("sameValueAs")
                ->with($this->equalTo(new DateTimeInterval($this->startTime, $this->endTime)))
                ->willReturn(true);
        $this->executeUpdate();
        $this->assertEquals([], $this->meeting->recordedEvents);
    }
    
    protected function executeCreateAttendee()
    {
        return $this->meeting->createAttendee($this->attendeeId, $this->activityParticipantType, $this->anInitiator);
    }
    public function test_createAttendee_returnAttendeeWithAttendeeSetupFromMeetingType()
    {
        $this->meetingType->expects($this->once())
                ->method('getActiveAttendeeSetupCorrenspondWithRoleOrDie')
                ->with($this->activityParticipantType)
                ->willReturn($attendeeSetup = $this->buildMockOfClass(ActivityParticipant::class));
        $attendee = new Attendee($this->meeting, $this->attendeeId, $attendeeSetup, $this->anInitiator);
        $this->assertEquals($attendee, $this->executeCreateAttendee());
    }
    
    public function test_createAttendee_appendAttendeeToEvent()
    {
        $this->executeCreateAttendee();
        $this->assertInstanceOf(Attendee::class, $this->meeting->aggregatedEntitiesHavingEvents[0]);
    }
    
    protected function executeInviteAllActiveProgramParticipants()
    {
        $this->startEndTime->expects($this->any())
                ->method('isUpcoming')
                ->willReturn(true);
        $this->meeting->inviteAllActiveProgramParticipants();
    }
    public function test_inviteAllActiveProgramParticipant_executeProgramsInviteAllActiveParticipantsToMeeting()
    {
        $this->meetingType->expects($this->once())
                ->method('inviteAllActiveProgramParticipantsToMeeting')
                ->with($this->meeting);
        $this->executeInviteAllActiveProgramParticipants();
    }
    public function test_inviteAllActiveProgramParticipants_notAnUpcomingMeeting_forbidden()
    {
        $this->startEndTime->expects($this->once())
                ->method('isUpcoming')
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function (){
            $this->executeInviteAllActiveProgramParticipants();
        }, 'Forbidden', 'forbidden: not an upcoming meeting');
    }
    
}

class TestableMeeting extends Meeting
{
    public $meetingType;
    public $id;
    public $name;
    public $description;
    public $startEndTime;
    public $location;
    public $note;
    public $cancelled;
    public $createdTime;
    
    public $recordedEvents = [];
    public $aggregatedEntitiesHavingEvents = [];

}
