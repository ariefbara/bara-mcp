<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\Program\MeetingType\ {
    Meeting\Attendee,
    MeetingData
};
use Tests\TestBase;

class UpdateMeetingTest extends TestBase
{
    protected $attendeeRepository, $attendee;
    protected $service;
    protected $firmId = "firmId", $managerId = "managerId", $meetingId = "meetingId";
    protected $meetingData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->attendee = $this->buildMockOfClass(Attendee::class);
        $this->attendeeRepository = $this->buildMockOfInterface(AttendeeRepository::class);
        $this->attendeeRepository->expects($this->any())
                ->method("anAttendeeBelongsToManagerCorrespondWithMeeting")
                ->with($this->firmId, $this->managerId, $this->meetingId)
                ->willReturn($this->attendee);
        
        $this->service = new UpdateMeeting($this->attendeeRepository);
        
        $this->meetingData = $this->buildMockOfClass(MeetingData::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->managerId, $this->meetingId, $this->meetingData);
    }
    public function test_execute_executeAttendeesUpdateMeeting()
    {
        $this->attendee->expects($this->once())
                ->method("updateMeeting")
                ->with($this->meetingData);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->attendeeRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
