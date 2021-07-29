<?php

namespace Firm\Domain\Model\Firm\Program\Coordinator;

use Firm\Domain\Model\Firm\Program\Coordinator;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\Attendee;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\ITaskExecutableByMeetingInitiator;
use SharedContext\Domain\ValueObject\ActivityParticipantType;
use Tests\TestBase;

class CoordinatorAttendeeTest extends TestBase
{
    protected $coordinator;
    protected $meeting;
    protected $coordinatorAttendee, $attendee;
    protected $id = 'new-id', $anInitiator = true;
    
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->meeting = $this->buildMockOfClass(Meeting::class);
        
        $this->coordinatorAttendee = new TestableCoordinatorAttendee($this->coordinator, 'id', $this->meeting, false);
        $this->attendee = $this->buildMockOfClass(Attendee::class);
        $this->coordinatorAttendee->attendee = $this->attendee;
        
        $this->task = $this->buildMockOfInterface(ITaskExecutableByMeetingInitiator::class);
    }
    
    protected function executeConstruct()
    {
        return new TestableCoordinatorAttendee($this->coordinator, $this->id, $this->meeting, $this->anInitiator);
    }
    public function test_construct_setProperties()
    {
        $coordinatorAttendee = $this->executeConstruct();
        $this->assertEquals($this->coordinator, $coordinatorAttendee->coordinator);
        $this->assertEquals($this->id, $coordinatorAttendee->id);
    }
    public function test_construct_setAttendeeCreatedInMeeting()
    {
        $this->meeting->expects($this->once())
                ->method('createAttendee')
                ->with($this->id, new ActivityParticipantType(ActivityParticipantType::COORDINATOR), $this->anInitiator)
                ->willReturn($this->attendee);
        $coordinatorAttendee = $this->executeConstruct();
        $this->assertEquals($this->attendee, $coordinatorAttendee->attendee);
    }
    
    public function test_isActiveAttendeeOfMeeting_returnAttendeeIsActiveAttendeeOfMeetingResult()
    {
        $this->attendee->expects($this->once())
                ->method('isActiveAttendeeOfMeeting')
                ->with($this->meeting);
        $this->coordinatorAttendee->isActiveAttendeeOfMeeting($this->meeting);
    }
    
    public function test_disabledValidInvitation_disableAttendeeValidInvitation()
    {
        $this->attendee->expects($this->once())
                ->method('disableValidInvitation');
        $this->coordinatorAttendee->disableValidInvitation();
    }
    
    public function test_executeTaskAsMeetingInitiator_attendeeExecuteTaskAsMeetingInitiator()
    {
        $this->attendee->expects($this->once())
                ->method('executeTaskAsMeetingInitiator')
                ->with($this->task);
        $this->coordinatorAttendee->executeTaskAsMeetingInitiator($this->task);
    }
}

class TestableCoordinatorAttendee extends CoordinatorAttendee
{
    public $coordinator;
    public $attendee;
    public $id;
}
