<?php

namespace Firm\Domain\Task\MeetingInitiator;

use Resources\Application\Event\Dispatcher;
use Tests\src\Firm\Domain\Task\MeetingInitiator\MeetingInitiatorTestBase;

class InviteAllProgramParticipantTaskTest extends MeetingInitiatorTestBase
{
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->task = new InviteAllProgramParticipantTask($this->dispatcher);
    }
    
    protected function execute()
    {
        $this->task->executeByMeetingInitiatorOf($this->meeting);
    }
    public function test_execute_executeMeetingInviteAllProgramParticipant()
    {
        $this->meeting->expects($this->once())
                ->method('inviteAllActiveProgramParticipants');
        $this->execute();
    }
    public function test_execute_dispatchMeeting()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->meeting);
        $this->execute();
    }
}
