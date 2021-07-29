<?php

namespace Firm\Domain\Task\MeetingInitiator;

use Firm\Domain\Model\Firm\Program\ActivityType\MeetingData;
use Tests\src\Firm\Domain\Task\MeetingInitiator\MeetingInitiatorTestBase;

class UpdateMeetingTaskTest extends MeetingInitiatorTestBase
{
    protected $meetingData;
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->meetingData = $this->buildMockOfClass(MeetingData::class);
        $this->task = new UpdateMeetingTask($this->meetingData, $this->dispatcher);
    }
    
    protected function execute()
    {
        $this->task->executeByMeetingInitiatorOf($this->meeting);
    }
    public function test_execute_updateMeeting()
    {
        $this->meeting->expects($this->once())
                ->method('update')
                ->with($this->meetingData);
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
