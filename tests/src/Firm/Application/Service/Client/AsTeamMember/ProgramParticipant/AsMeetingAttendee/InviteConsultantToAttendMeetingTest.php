<?php

namespace Firm\Application\Service\Client\AsTeamMember\ProgramParticipant\AsMeetingAttendee;

use Firm\ {
    Application\Service\Client\AsTeamMember\TeamMemberRepository,
    Application\Service\Firm\Program\ConsultantRepository,
    Domain\Model\Firm\Program\Consultant,
    Domain\Model\Firm\Team\Member,
    Domain\Service\MeetingAttendeeBelongsToTeamFinder
};
use Tests\TestBase;

class InviteConsultantToAttendMeetingTest extends TestBase
{
    protected $teamMemberRepository, $teamMember;
    protected $meetingAttendeeBelongsToTeamFinder;
    protected $consultant, $consultantRepository;
    protected $service;
    protected $firmId = "firmId", $clientId = "clientId", $teamId = "teamId", $meetingId = "meetingId", $consultantId = "consultantId";

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->teamMember = $this->buildMockOfClass(Member::class);
        $this->teamMemberRepository = $this->buildMockOfInterface(TeamMemberRepository::class);
        $this->teamMemberRepository->expects($this->any())
                ->method("aTeamMemberCorrespondWithTeam")
                ->with($this->firmId, $this->clientId, $this->teamId)
                ->willReturn($this->teamMember);
        
        $this->meetingAttendeeBelongsToTeamFinder = $this->buildMockOfClass(MeetingAttendeeBelongsToTeamFinder::class);
        
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->consultantRepository = $this->buildMockOfInterface(ConsultantRepository::class);
        $this->consultantRepository->expects($this->once())
                ->method("aConsultantOfId")
                ->with($this->consultantId)
                ->willReturn($this->consultant);
        
        $this->service = new InviteConsultantToAttendMeeting(
                $this->teamMemberRepository, $this->meetingAttendeeBelongsToTeamFinder, $this->consultantRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->teamId, $this->meetingId, $this->consultantId);
    }
    public function test_execute_inviteConsultantToAttendMeeting()
    {
        $this->teamMember->expects($this->once())
                ->method("inviteUserToAttendMeeting")
                ->with($this->meetingAttendeeBelongsToTeamFinder, $this->meetingId, $this->consultant);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->teamMemberRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
