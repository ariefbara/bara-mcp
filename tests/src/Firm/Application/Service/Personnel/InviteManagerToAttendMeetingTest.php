<?php

namespace Firm\Application\Service\Personnel;

use Firm\ {
    Application\Service\Firm\ManagerRepository,
    Domain\Model\Firm\Manager,
    Domain\Model\Firm\Program\MeetingType\Meeting\Attendee
};
use Tests\TestBase;

class InviteManagerToAttendMeetingTest extends TestBase
{
    protected $meetingAttendanceRepository, $meetingAttendance;
    protected $manager, $managerRepository;
    protected $service;
    protected $firmId = "firmId", $personnelId = "personnelId", $meetingId = "meetingId", $managerId = "managerId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->meetingAttendance = $this->buildMockOfClass(Attendee::class);
        $this->meetingAttendanceRepository = $this->buildMockOfInterface(MeetingAttendanceRepository::class);
        $this->meetingAttendanceRepository->expects($this->any())
                ->method("aMeetingAttendanceBelongsToPersonnelCorrespondWithMeeting")
                ->with($this->firmId, $this->personnelId, $this->meetingId)
                ->willReturn($this->meetingAttendance);
        
        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->managerRepository = $this->buildMockOfInterface(ManagerRepository::class);
        $this->managerRepository->expects($this->once())
                ->method("aManagerOfId")
                ->with($this->managerId)
                ->willReturn($this->manager);
        
        $this->service = new InviteManagerToAttendMeeting($this->meetingAttendanceRepository, $this->managerRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->personnelId, $this->meetingId, $this->managerId);
    }
    public function test_execute_inviteManagerToAttendMeeting()
    {
        $this->meetingAttendance->expects($this->once())
                ->method("inviteUserToAttendMeeting")
                ->with($this->manager);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->meetingAttendanceRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
