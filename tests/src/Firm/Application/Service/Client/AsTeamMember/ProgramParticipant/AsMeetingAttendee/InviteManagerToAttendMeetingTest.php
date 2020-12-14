<?php

namespace Firm\Application\Service\Client\AsTeamMember\ProgramParticipant\AsMeetingAttendee;

use Firm\Application\Service\Client\AsTeamMember\TeamMemberRepository;
use Firm\Application\Service\Firm\ManagerRepository;
use Firm\Domain\Model\Firm\Manager;
use Firm\Domain\Model\Firm\Team\Member;
use Firm\Domain\Service\MeetingAttendeeBelongsToTeamFinder;
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class InviteManagerToAttendMeetingTest extends TestBase
{
    protected $teamMemberRepository, $teamMember;
    protected $meetingAttendeeBelongsToTeamFinder;
    protected $manager, $managerRepository;
    protected $dispatcher;
    protected $service;
    protected $firmId = "firmId", $clientId = "clientId", $teamId = "teamId", $meetingId = "meetingId", $managerId = "managerId";

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
        
        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->managerRepository = $this->buildMockOfInterface(ManagerRepository::class);
        $this->managerRepository->expects($this->once())
                ->method("aManagerOfId")
                ->with($this->managerId)
                ->willReturn($this->manager);
        
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);
        
        $this->service = new InviteManagerToAttendMeeting(
                $this->teamMemberRepository, $this->meetingAttendeeBelongsToTeamFinder, $this->managerRepository, $this->dispatcher);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->teamId, $this->meetingId, $this->managerId);
    }
    public function test_execute_inviteManagerToAttendMeeting()
    {
        $this->teamMember->expects($this->once())
                ->method("inviteUserToAttendMeeting")
                ->with($this->meetingAttendeeBelongsToTeamFinder, $this->meetingId, $this->manager);
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

