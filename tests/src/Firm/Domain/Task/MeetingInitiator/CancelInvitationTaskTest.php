<?php

namespace Firm\Domain\Task\MeetingInitiator;

use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\Attendee;
use Tests\src\Firm\Domain\Task\MeetingInitiator\MeetingInitiatorTestBase;

class CancelInvitationTaskTest extends MeetingInitiatorTestBase
{
    protected $attendeeRepository, $attendee, $attendeeId = 'attendee-id';
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->attendee = $this->buildMockOfClass(Attendee::class);
        $this->attendeeRepository = $this->buildMockOfInterface(AttendeeRepository::class);
        $this->attendeeRepository->expects($this->any())
                ->method('ofId')
                ->with($this->attendeeId)
                ->willReturn($this->attendee);
        
        $this->task = new CancelInvitationTask($this->attendeeRepository, $this->attendeeId, $this->dispatcher);
    }
    
    protected function execute()
    {
        $this->task->executeByMeetingInitiatorOf($this->meeting);
    }
    public function test_execute_cancelInvitation()
    {
        $this->attendee->expects($this->once())
                ->method('cancel');
        $this->execute();
    }
    public function test_execute_assertAttendeeIsManageableInMeeting()
    {
        $this->attendee->expects($this->once())
                ->method('assertManageableInMeeting')
                ->with($this->meeting);
        $this->execute();
    }
    public function test_execute_dispatchAttendee()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->attendee);
        $this->execute();
    }
    
}
