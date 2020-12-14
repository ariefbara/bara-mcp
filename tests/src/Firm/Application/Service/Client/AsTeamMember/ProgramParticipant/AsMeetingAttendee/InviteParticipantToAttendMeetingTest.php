<?php

namespace Firm\Application\Service\Client\AsTeamMember\ProgramParticipant\AsMeetingAttendee;

use Firm\Application\Service\Client\AsTeamMember\TeamMemberRepository;
use Firm\Application\Service\Firm\Program\ParticipantRepository;
use Firm\Domain\Model\Firm\Program\Participant;
use Firm\Domain\Model\Firm\Team\Member;
use Firm\Domain\Service\MeetingAttendeeBelongsToTeamFinder;
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class InviteParticipantToAttendMeetingTest extends TestBase
{
    protected $teamMemberRepository, $teamMember;
    protected $meetingAttendeeBelongsToTeamFinder;
    protected $participant, $participantRepository;
    protected $dispatcher;
    protected $service;
    protected $firmId = "firmId", $clientId = "clientId", $teamId = "teamId", $meetingId = "meetingId", $participantId = "participantId";

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
        
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->participantRepository = $this->buildMockOfInterface(ParticipantRepository::class);
        $this->participantRepository->expects($this->once())
                ->method("ofId")
                ->with($this->participantId)
                ->willReturn($this->participant);
        
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);
        
        $this->service = new InviteParticipantToAttendMeeting(
                $this->teamMemberRepository, $this->meetingAttendeeBelongsToTeamFinder, $this->participantRepository, $this->dispatcher);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->teamId, $this->meetingId, $this->participantId);
    }
    public function test_execute_inviteParticipantToAttendMeeting()
    {
        $this->teamMember->expects($this->once())
                ->method("inviteUserToAttendMeeting")
                ->with($this->meetingAttendeeBelongsToTeamFinder, $this->meetingId, $this->participant);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->teamMemberRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
    public function test_execute_dispatchTeamMember()
    {
        $this->dispatcher->expects($this->once())
                ->method("dispatch")
                ->with($this->teamMember);
        $this->execute();
    }
}

