<?php

namespace Firm\Domain\Task\MeetingInitiator;

use Firm\Domain\Model\Firm\Program\CanAttendMeeting;
use Tests\src\Firm\Domain\Task\MeetingInitiator\MeetingInitiatorTestBase;

class InviteUserTaskTest extends MeetingInitiatorTestBase
{
    protected $userRepository, $user, $userId = 'user-id';
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->buildMockOfInterface(CanAttendMeeting::class);
        $this->userRepository = $this->buildMockOfInterface(UserRepository::class);
        $this->userRepository->expects($this->any())
                ->method('aUserOfId')
                ->with($this->userId)
                ->willReturn($this->user);
        
        $this->task = new InviteUserTask($this->userRepository, $this->userId, $this->dispatcher);
    }
    
    protected function execute()
    {
        $this->task->executeByMeetingInitiatorOf($this->meeting);
    }
    
    public function test_executeByMeetingInitiator_meetingInviteUser()
    {
        $this->user->expects($this->once())
                ->method('inviteToMeeting')
                ->with($this->meeting);
        $this->execute();
    }
    public function test_execute_dispatcheMeeting()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->meeting);
        $this->execute();
    }
}
