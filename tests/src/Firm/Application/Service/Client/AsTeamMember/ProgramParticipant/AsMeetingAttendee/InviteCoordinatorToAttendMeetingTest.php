<?php

namespace Firm\Application\Service\Client\AsTeamMember\ProgramParticipant\AsMeetingAttendee;

use Firm\Application\Service\Client\AsTeamMember\TeamMemberRepository;
use Firm\Application\Service\Firm\Program\CoordinatorRepository;
use Firm\Domain\Model\Firm\Program\Coordinator;
use Firm\Domain\Model\Firm\Team\Member;
use Firm\Domain\Service\MeetingAttendeeBelongsToTeamFinder;
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class InviteCoordinatorToAttendMeetingTest extends TestBase
{
    protected $teamMemberRepository, $teamMember;
    protected $meetingAttendeeBelongsToTeamFinder;
    protected $coordinator, $coordinatorRepository;
    protected $dispatcher;
    protected $service;
    protected $firmId = "firmId", $clientId = "clientId", $teamId = "teamId", $meetingId = "meetingId", $coordinatorId = "coordinatorId";

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
        
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->coordinatorRepository = $this->buildMockOfInterface(CoordinatorRepository::class);
        $this->coordinatorRepository->expects($this->once())
                ->method("aCoordinatorOfId")
                ->with($this->coordinatorId)
                ->willReturn($this->coordinator);
        
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);
        
        $this->service = new InviteCoordinatorToAttendMeeting(
                $this->teamMemberRepository, $this->meetingAttendeeBelongsToTeamFinder, $this->coordinatorRepository, $this->dispatcher);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->teamId, $this->meetingId, $this->coordinatorId);
    }
    public function test_execute_inviteCoordinatorToAttendMeeting()
    {
        $this->teamMember->expects($this->once())
                ->method("inviteUserToAttendMeeting")
                ->with($this->meetingAttendeeBelongsToTeamFinder, $this->meetingId, $this->coordinator);
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
