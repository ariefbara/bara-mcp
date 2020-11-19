<?php

namespace Firm\Application\Service\Personnel;

use Firm\ {
    Application\Service\Firm\Program\ParticipantRepository,
    Domain\Model\Firm\Program\MeetingType\Meeting\Attendee,
    Domain\Model\Firm\Program\Participant
};
use Tests\TestBase;

class InviteParticipantToAttendMeetingTest extends TestBase
{
    protected $meetingAttendanceRepository, $meetingAttendance;
    protected $participant, $participantRepository;
    protected $service;
    protected $firmId = "firmId", $personnelId = "personnelId", $meetingId = "meetingId", $participantId = "participantId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->meetingAttendance = $this->buildMockOfClass(Attendee::class);
        $this->meetingAttendanceRepository = $this->buildMockOfInterface(MeetingAttendanceRepository::class);
        $this->meetingAttendanceRepository->expects($this->any())
                ->method("aMeetingAttendanceBelongsToPersonnelCorrespondWithMeeting")
                ->with($this->firmId, $this->personnelId, $this->meetingId)
                ->willReturn($this->meetingAttendance);
        
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->participantRepository = $this->buildMockOfInterface(ParticipantRepository::class);
        $this->participantRepository->expects($this->once())
                ->method("ofId")
                ->with($this->participantId)
                ->willReturn($this->participant);
        
        $this->service = new InviteParticipantToAttendMeeting($this->meetingAttendanceRepository, $this->participantRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->personnelId, $this->meetingId, $this->participantId);
    }
    public function test_execute_inviteParticipantToAttendMeeting()
    {
        $this->meetingAttendance->expects($this->once())
                ->method("inviteUserToAttendMeeting")
                ->with($this->participant);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->meetingAttendanceRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
