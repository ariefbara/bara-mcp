<?php

namespace Firm\Application\Service\Personnel;

use Firm\Domain\Model\Firm\Program\MeetingType\ {
    Meeting\Attendee,
    MeetingData
};
use Tests\TestBase;

class UpdateMeetingTest extends TestBase
{
    protected $meetingAttendanceRepository, $meetingAttendance;
    protected $service;
    protected $firmId = "firmId", $personnelId = "personnelId", $meetingId = "meetingId";
    protected $meetingData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->meetingAttendance = $this->buildMockOfClass(Attendee::class);
        $this->meetingAttendanceRepository = $this->buildMockOfInterface(MeetingAttendanceRepository::class);
        $this->meetingAttendanceRepository->expects($this->any())
                ->method("aMeetingAttendanceBelongsToPersonnelCorrespondWithMeeting")
                ->with($this->firmId, $this->personnelId, $this->meetingId)
                ->willReturn($this->meetingAttendance);
        
        $this->service = new UpdateMeeting($this->meetingAttendanceRepository);
        
        $this->meetingData = $this->buildMockOfClass(MeetingData::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->personnelId, $this->meetingId, $this->meetingData);
    }
    public function test_execute_executeAttendeesUpdateMeeting()
    {
        $this->meetingAttendance->expects($this->once())
                ->method("updateMeeting")
                ->with($this->meetingData);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->meetingAttendanceRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
