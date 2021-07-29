<?php

namespace Firm\Domain\Model\Firm\Manager;

use Firm\Domain\Model\Firm\Manager;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\Attendee;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\ITaskExecutableByMeetingInitiator;
use SharedContext\Domain\ValueObject\ActivityParticipantType;
use Tests\TestBase;

class ManagerAttendeeTest extends TestBase
{
    protected $manager;
    protected $meeting;
    protected $managerAttendee, $attendee;
    protected $id = 'new-id', $anInitiator = true;
    
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->meeting = $this->buildMockOfClass(Meeting::class);
        
        $this->managerAttendee = new TestableManagerAttendee($this->manager, 'id', $this->meeting, false);
        $this->attendee = $this->buildMockOfClass(Attendee::class);
        $this->managerAttendee->attendee = $this->attendee;
        
        $this->task = $this->buildMockOfInterface(ITaskExecutableByMeetingInitiator::class);
    }
    
    protected function executeConstruct()
    {
        return new TestableManagerAttendee($this->manager, $this->id, $this->meeting, $this->anInitiator);
    }
    public function test_construct_setProperties()
    {
        $managerAttendee = $this->executeConstruct();
        $this->assertEquals($this->manager, $managerAttendee->manager);
        $this->assertEquals($this->id, $managerAttendee->id);
    }
    public function test_construct_setAttendeeCreatedInMeeting()
    {
        $this->meeting->expects($this->once())
                ->method('createAttendee')
                ->with($this->id, new ActivityParticipantType(ActivityParticipantType::MANAGER), $this->anInitiator)
                ->willReturn($this->attendee);
        $managerAttendee = $this->executeConstruct();
        $this->assertEquals($this->attendee, $managerAttendee->attendee);
    }
    
    public function test_isActiveAttendeeOfMeeting_returnAttendeeIsActiveAttendeeOfMeetingResult()
    {
        $this->attendee->expects($this->once())
                ->method('isActiveAttendeeOfMeeting')
                ->with($this->meeting);
        $this->managerAttendee->isActiveAttendeeOfMeeting($this->meeting);
    }
    
    public function test_executeTaskAsMeetingInitiator_attendeeExecuteTaskAsMeetingInitiator()
    {
        $this->attendee->expects($this->once())
                ->method('executeTaskAsMeetingInitiator')
                ->with($this->task);
        $this->managerAttendee->executeTaskAsMeetingInitiator($this->task);
    }
}

class TestableManagerAttendee extends ManagerAttendee
{
    public $manager;
    public $attendee;
    public $id;
}
