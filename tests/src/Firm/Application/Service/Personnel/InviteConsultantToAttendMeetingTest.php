<?php

namespace Firm\Application\Service\Personnel;

use Firm\ {
    Application\Service\Firm\Program\ConsultantRepository,
    Domain\Model\Firm\Program\Consultant,
    Domain\Model\Firm\Program\MeetingType\Meeting\Attendee
};
use Tests\TestBase;

class InviteConsultantToAttendMeetingTest extends TestBase
{
    protected $attendeeRepository, $attendee;
    protected $consultant, $consultantRepository;
    protected $service;
    protected $firmId = "firmId", $personnelId = "personnelId", $meetingId = "meetingId", $consultantId = "consultantId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->attendee = $this->buildMockOfClass(Attendee::class);
        $this->attendeeRepository = $this->buildMockOfInterface(AttendeeRepository::class);
        $this->attendeeRepository->expects($this->any())
                ->method("anAttendeeBelongsToPersonnelCorrespondWithMeeting")
                ->with($this->firmId, $this->personnelId, $this->meetingId)
                ->willReturn($this->attendee);
        
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->consultantRepository = $this->buildMockOfInterface(ConsultantRepository::class);
        $this->consultantRepository->expects($this->once())
                ->method("aConsultantOfId")
                ->with($this->consultantId)
                ->willReturn($this->consultant);
        
        $this->service = new InviteConsultantToAttendMeeting($this->attendeeRepository, $this->consultantRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->personnelId, $this->meetingId, $this->consultantId);
    }
    public function test_execute_inviteConsultantToAttendMeeting()
    {
        $this->attendee->expects($this->once())
                ->method("inviteUserToAttendMeeting")
                ->with($this->consultant);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->attendeeRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
