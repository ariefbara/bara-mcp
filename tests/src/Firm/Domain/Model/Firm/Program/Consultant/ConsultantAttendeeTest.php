<?php

namespace Firm\Domain\Model\Firm\Program\Consultant;

use Firm\Domain\Model\Firm\Program\Consultant;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\Attendee;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\ITaskExecutableByMeetingInitiator;
use SharedContext\Domain\ValueObject\ActivityParticipantType;
use Tests\TestBase;

class ConsultantAttendeeTest extends TestBase
{
    protected $consultant;
    protected $meeting;
    protected $consultantAttendee, $attendee;
    protected $id = 'new-id', $anInitiator = true;
    
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->meeting = $this->buildMockOfClass(Meeting::class);
        
        $this->consultantAttendee = new TestableConsultantAttendee($this->consultant, 'id', $this->meeting, false);
        $this->attendee = $this->buildMockOfClass(Attendee::class);
        $this->consultantAttendee->attendee = $this->attendee;
        
        $this->attendee->expects($this->any())
                ->method('getMeeting')
                ->willReturn($this->meeting);
        
        $this->task = $this->buildMockOfInterface(ITaskExecutableByMeetingInitiator::class);
    }
    
    protected function executeConstruct()
    {
        return new TestableConsultantAttendee($this->consultant, $this->id, $this->meeting, $this->anInitiator);
    }
    public function test_construct_setProperties()
    {
        $consultantAttendee = $this->executeConstruct();
        $this->assertEquals($this->consultant, $consultantAttendee->consultant);
        $this->assertEquals($this->id, $consultantAttendee->id);
    }
    public function test_construct_setAttendeeCreatedInMeeting()
    {
        $this->meeting->expects($this->once())
                ->method('createAttendee')
                ->with($this->id, new ActivityParticipantType(ActivityParticipantType::CONSULTANT), $this->anInitiator)
                ->willReturn($this->attendee);
        $consultantAttendee = $this->executeConstruct();
        $this->assertEquals($this->attendee, $consultantAttendee->attendee);
    }
    
    public function test_isActiveAttendeeOfMeeting_returnAttendeeIsActiveAttendeeOfMeetingResult()
    {
        $this->attendee->expects($this->once())
                ->method('isActiveAttendeeOfMeeting')
                ->with($this->meeting);
        $this->consultantAttendee->isActiveAttendeeOfMeeting($this->meeting);
    }
    
    public function test_disabledValidInvitation_disableAttendeeValidInvitation()
    {
        $this->attendee->expects($this->once())
                ->method('disableValidInvitation');
        $this->consultantAttendee->disableValidInvitation();
    }
    
    protected function executeInviteAllActiveDedicatedMentees()
    {
        $this->consultantAttendee->inviteAllActiveDedicatedMentees();
    }
    public function test_inviteAllActiveDedicatedMentees_executeConsultantInviteAllActiveDedicatedMenteesToMeeting()
    {
        $this->consultant->expects($this->once())
                ->method('inviteAllActiveDedicatedMenteesToMeeting')
                ->with($this->meeting);
        $this->executeInviteAllActiveDedicatedMentees();
    }
    public function test_inviteAllActiveDedicatedMentees_assertActiveInitiator()
    {
        $this->attendee->expects($this->once())
                ->method('assertActiveInitiator');
        $this->executeInviteAllActiveDedicatedMentees();
    }
    public function test_inviteAllActiveDedicatedMentees_registerConsultantAsEventSource()
    {
        $this->executeInviteAllActiveDedicatedMentees();
        $this->assertEquals($this->consultant, $this->consultantAttendee->aggregatedEntitiesHavingEvents[0]);
    }
    
    public function test_executeTaskAsMeetingInitiator_attendeeExecuteTaskAsMeetingInitiator()
    {
        $this->attendee->expects($this->once())
                ->method('executeTaskAsMeetingInitiator')
                ->with($this->task);
        $this->consultantAttendee->executeTaskAsMeetingInitiator($this->task);
    }
}

class TestableConsultantAttendee extends ConsultantAttendee
{
    public $consultant;
    public $attendee;
    public $id;
    public $aggregatedEntitiesHavingEvents;
}
