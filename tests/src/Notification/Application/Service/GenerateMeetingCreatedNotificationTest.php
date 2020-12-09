<?php

namespace Notification\Application\Service;

use Notification\Domain\Model\Firm\Program\MeetingType\Meeting;
use Tests\TestBase;

class GenerateMeetingCreatedNotificationTest extends TestBase
{
    protected $meetingRepository, $meeting;
    protected $service;
    protected $meetingId = "meetingId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->meeting = $this->buildMockOfClass(Meeting::class);
        $this->meetingRepository = $this->buildMockOfInterface(MeetingRepository::class);
        $this->meetingRepository->expects($this->any())
                ->method("ofId")
                ->with($this->meetingId)
                ->willReturn($this->meeting);
        
        $this->service = new GenerateMeetingCreatedNotification($this->meetingRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->meetingId);
    }
    public function test_execute_addMeetingCreatedNotificationInMeeting()
    {
        $this->meeting->expects($this->once())
                ->method("addMeetingCreatedNotification");
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->meetingRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
