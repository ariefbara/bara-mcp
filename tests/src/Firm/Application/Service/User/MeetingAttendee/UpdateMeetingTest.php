<?php

namespace Firm\Application\Service\User\MeetingAttendee;

use Firm\Domain\Model\Firm\Program\ActivityType\MeetingType\Meeting\Attendee;
use Firm\Domain\Model\Firm\Program\ActivityType\MeetingType\MeetingData;
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class UpdateMeetingTest extends TestBase
{
    protected $attendeeRepository, $attendee;
    protected $dispatcher;
    protected $service;
    protected $userId = "userId", $meetingId = "meetingId";
    protected $meetingData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->attendee = $this->buildMockOfClass(Attendee::class);
        $this->attendeeRepository = $this->buildMockOfInterface(AttendeeRepository::class);
        $this->attendeeRepository->expects($this->any())
                ->method("anAttendeeBelongsToUserParticipantCorrespondWithMeeting")
                ->with($this->userId, $this->meetingId)
                ->willReturn($this->attendee);
        
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);
        
        $this->service = new UpdateMeeting($this->attendeeRepository, $this->dispatcher);
        
        $this->meetingData = $this->buildMockOfClass(MeetingData::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->userId, $this->meetingId, $this->meetingData);
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
    public function test_execute_dispatchAttendee()
    {
        $this->dispatcher->expects($this->once())
                ->method("dispatch")
                ->with($this->identicalTo($this->attendee));
        $this->execute();
    }
}
