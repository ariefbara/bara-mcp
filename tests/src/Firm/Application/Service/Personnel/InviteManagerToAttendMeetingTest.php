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
    protected $attendeeRepository, $attendee;
    protected $manager, $managerRepository;
    protected $service;
    protected $firmId = "firmId", $personnelId = "personnelId", $meetingId = "meetingId", $managerId = "managerId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->attendee = $this->buildMockOfClass(Attendee::class);
        $this->attendeeRepository = $this->buildMockOfInterface(AttendeeRepository::class);
        $this->attendeeRepository->expects($this->any())
                ->method("anAttendeeBelongsToPersonnelCorrespondWithMeeting")
                ->with($this->firmId, $this->personnelId, $this->meetingId)
                ->willReturn($this->attendee);
        
        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->managerRepository = $this->buildMockOfInterface(ManagerRepository::class);
        $this->managerRepository->expects($this->once())
                ->method("aManagerOfId")
                ->with($this->managerId)
                ->willReturn($this->manager);
        
        $this->service = new InviteManagerToAttendMeeting($this->attendeeRepository, $this->managerRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->personnelId, $this->meetingId, $this->managerId);
    }
    public function test_execute_inviteManagerToAttendMeeting()
    {
        $this->attendee->expects($this->once())
                ->method("inviteUserToAttendMeeting")
                ->with($this->manager);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->attendeeRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
