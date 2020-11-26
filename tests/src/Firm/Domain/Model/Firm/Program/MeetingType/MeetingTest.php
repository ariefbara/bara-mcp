<?php

namespace Firm\Domain\Model\Firm\Program\MeetingType;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\Model\Firm\Program\ {
    ActivityType,
    ActivityType\ActivityParticipant,
    MeetingType\Meeting\Attendee
};
use Resources\ {
    DateTimeImmutableBuilder,
    Domain\ValueObject\DateTimeInterval
};
use Tests\TestBase;

class MeetingTest extends TestBase
{

    protected $meetingType;
    protected $user;
    protected $meeting;
    protected $attendee;
    protected $id = "newId", $name = "new name", $description = "new description ", $startTime, $endTime,
            $location = "new location", $note = "new note";
    protected $attendeeSetup;

    protected function setUp(): void
    {
        parent::setUp();
        $this->meetingType = $this->buildMockOfClass(ActivityType::class);
        $this->user = $this->buildMockOfInterface(CanAttendMeeting::class);
        $meetingData = new MeetingData(
                "name", "description", new DateTimeImmutable("+24 hours"), new DateTimeImmutable("+25 hours"),
                "location", "note");

        $this->meeting = new TestableMeeting($this->meetingType, "id", $meetingData, $this->user);
        
        $this->attendee = $this->buildMockOfClass(Attendee::class);
        $this->meeting->attendees->add($this->attendee);
        
        $this->startTime = new DateTimeImmutable("+48 hours");
        $this->endTime = new DateTimeImmutable("+50 hours");
        
        $this->attendeeSetup = $this->buildMockOfClass(ActivityParticipant::class);
    }
    
    protected function getMeetingData()
    {
        return new MeetingData($this->name, $this->description, $this->startTime, $this->endTime, $this->location, $this->note);
    }
    
    protected function executeConstruct()
    {
        return new TestableMeeting($this->meetingType, $this->id, $this->getMeetingData(), $this->user);
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
        
        $this->assertInstanceOf(ArrayCollection::class, $meeting->attendees);
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
    public function test_construct_setupInitiatorThroughtMeetingType()
    {
        $this->meetingType->expects($this->once())
                ->method("setUserAsInitiatorInMeeting")
                ->with($this->anything(), $this->user);
        $this->executeConstruct();
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
    
    public function test_setInitiator_setUserAsAttendeeWithIntiatorRole()
    {
        $this->meeting->setInitiator($this->attendeeSetup, $this->user);
        $this->assertInstanceOf(Attendee::class, $this->meeting->attendees->first());
    }
    
    public function test_inviteUser_addUserAsAttendeeThrougMeetingType()
    {
        $this->meetingType->expects($this->once())
                ->method("addUserAsAttendeeInMeeting")
                ->with($this->meeting, $this->user);
        $this->meeting->inviteUser($this->user);
    }
    
    protected function executeAddAttendee(): void
    {
        $this->meeting->addAttendee($this->attendeeSetup, $this->user);
    }
    public function test_addAttendee_addAttendeeToList()
    {
        $this->executeAddAttendee();
        $this->assertEquals(2, $this->meeting->attendees->count());
        $this->assertInstanceOf(Attendee::class, $this->meeting->attendees->last());
    }
    public function test_addAttendee_anAttendeeCorrespondToUserExistInList_reinviteThisAttendee()
    {
        $this->attendee->expects($this->once())
                ->method("correspondWithUser")
                ->with($this->user)
                ->willReturn(true);
        $this->attendee->expects($this->once())
                ->method("reinvite");
        $this->executeAddAttendee();
    }
    public function test_addAttendee_anAttendeeCorrespondToRecipientExist_preventAddNewAttendee()
    {
        $this->attendee->expects($this->once())
                ->method("correspondWithUser")
                ->willReturn(true);
        $this->executeAddAttendee();
        $this->assertEquals(1, $this->meeting->attendees->count());
        
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
    public $attendees;

}
