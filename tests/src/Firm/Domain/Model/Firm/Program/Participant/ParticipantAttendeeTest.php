<?php

namespace Firm\Domain\Model\Firm\Program\Participant;

use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\Attendee;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\ITaskExecutableByMeetingInitiator;
use Firm\Domain\Model\Firm\Program\Participant;
use SharedContext\Domain\ValueObject\ActivityParticipantType;
use Tests\TestBase;

class ParticipantAttendeeTest extends TestBase
{
    protected $participant;
    protected $meeting;
    protected $participantAttendee, $attendee;
    protected $id = 'new-id', $anInitiator = true;
    
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->meeting = $this->buildMockOfClass(Meeting::class);
        
        $this->participantAttendee = new TestableParticipantAttendee($this->participant, 'id', $this->meeting, false);
        $this->attendee = $this->buildMockOfClass(Attendee::class);
        $this->participantAttendee->attendee = $this->attendee;
        
        $this->task = $this->buildMockOfInterface(ITaskExecutableByMeetingInitiator::class);
    }
    
    protected function executeConstruct()
    {
        return new TestableParticipantAttendee($this->participant, $this->id, $this->meeting, $this->anInitiator);
    }
    public function test_construct_setProperties()
    {
        $participantAttendee = $this->executeConstruct();
        $this->assertEquals($this->participant, $participantAttendee->participant);
        $this->assertEquals($this->id, $participantAttendee->id);
    }
    public function test_construct_setAttendeeCreatedInMeeting()
    {
        $this->meeting->expects($this->once())
                ->method('createAttendee')
                ->with($this->id, new ActivityParticipantType(ActivityParticipantType::PARTICIPANT), $this->anInitiator)
                ->willReturn($this->attendee);
        $participantAttendee = $this->executeConstruct();
        $this->assertEquals($this->attendee, $participantAttendee->attendee);
    }
    
    protected function executeAssertBelongsToParticipant()
    {
        $this->participantAttendee->assertBelongsToParticipant($this->participant);
    }
    public function test_assertBelongsToParticipant_sameParticipant_void()
    {
        $this->executeAssertBelongsToParticipant();
        $this->markAsSuccess();
    }
    public function test_assertBelongsToParticipant_differentParticipant_forbidden()
    {
        $this->participantAttendee->participant = $this->buildMockOfClass(Participant::class);
        $this->assertRegularExceptionThrowed(function (){
            $this->executeAssertBelongsToParticipant();
        }, 'Forbidden', "forbidden: attendee doesn't belongs to participant");
    }
    
    public function test_isActiveAttendeeOfMeeting_returnAttendeeIsActiveAttendeeOfMeetingResult()
    {
        $this->attendee->expects($this->once())
                ->method('isActiveAttendeeOfMeeting')
                ->with($this->meeting);
        $this->participantAttendee->isActiveAttendeeOfMeeting($this->meeting);
    }
    
    public function test_disabledValidInvitation_disableAttendeeValidInvitation()
    {
        $this->attendee->expects($this->once())
                ->method('disableValidInvitation');
        $this->participantAttendee->disableValidInvitation();
    }
    
    public function test_executeTaskAsMeetingInitiator_attendeeExecuteTaskAsMeetingInitiator()
    {
        $this->attendee->expects($this->once())
                ->method('executeTaskAsMeetingInitiator')
                ->with($this->task);
        $this->participantAttendee->executeTaskAsMeetingInitiator($this->task);
    }
}

class TestableParticipantAttendee extends ParticipantAttendee
{
    public $participant;
    public $attendee;
    public $id;
}
