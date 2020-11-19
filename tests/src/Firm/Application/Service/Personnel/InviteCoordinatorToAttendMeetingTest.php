<?php

namespace Firm\Application\Service\Personnel;

use Firm\ {
    Application\Service\Firm\Program\CoordinatorRepository,
    Application\Service\Personnel\MeetingAttendanceRepository,
    Domain\Model\Firm\Program\Coordinator,
    Domain\Model\Firm\Program\MeetingType\Meeting\Attendee
};
use Tests\TestBase;

class InviteCoordinatorToAttendMeetingTest extends TestBase
{
    protected $meetingAttendanceRepository, $meetingAttendance;
    protected $coordinator, $coordinatorRepository;
    protected $service;
    protected $firmId = "firmId", $personnelId = "personnelId", $meetingId = "meetingId", $coordinatorId = "coordinatorId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->meetingAttendance = $this->buildMockOfClass(Attendee::class);
        $this->meetingAttendanceRepository = $this->buildMockOfInterface(MeetingAttendanceRepository::class);
        $this->meetingAttendanceRepository->expects($this->any())
                ->method("aMeetingAttendanceBelongsToPersonnelCorrespondWithMeeting")
                ->with($this->firmId, $this->personnelId, $this->meetingId)
                ->willReturn($this->meetingAttendance);
        
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->coordinatorRepository = $this->buildMockOfInterface(CoordinatorRepository::class);
        $this->coordinatorRepository->expects($this->once())
                ->method("aCoordinatorOfId")
                ->with($this->coordinatorId)
                ->willReturn($this->coordinator);
        
        $this->service = new InviteCoordinatorToAttendMeeting($this->meetingAttendanceRepository, $this->coordinatorRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->personnelId, $this->meetingId, $this->coordinatorId);
    }
    public function test_execute_inviteCoordinatorToAttendMeeting()
    {
        $this->meetingAttendance->expects($this->once())
                ->method("inviteUserToAttendMeeting")
                ->with($this->coordinator);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->meetingAttendanceRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
