<?php

namespace Firm\Application\Service\Client\AsTeamMember\ProgramParticipant\AsMeetingAttendee;

use Firm\Application\Service\Client\AsTeamMember\TeamMemberRepository;
use Firm\Application\Service\Firm\Program\MeetingType\Meeting\AttendeeRepository;
use Firm\Domain\Model\Firm\Program\ActivityType\MeetingType\Meeting\Attendee;
use Firm\Domain\Model\Firm\Team\Member;
use Firm\Domain\Service\MeetingAttendeeBelongsToTeamFinder;
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class CancelInvitationTest extends TestBase
{
    protected $teamMemberRepository, $teamMember;
    protected $meetingAttendeeBelongsToTeamFinder;
    protected $attendeeRepository, $attendee;
    protected $dispatcher;
    protected $service;
    protected $firmId = "firmId", $clientId = "clientId", $teamId = "teamId", $meetingId = "meetingId", $attendeeId = "attendeeId";

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
        
        $this->attendee = $this->buildMockOfClass(Attendee::class);
        $this->attendeeRepository = $this->buildMockOfInterface(AttendeeRepository::class);
        $this->attendeeRepository->expects($this->any())
                ->method("ofId")
                ->with($this->attendeeId)
                ->willReturn($this->attendee);
        
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);
        
        $this->service = new CancelInvitation(
                $this->teamMemberRepository, $this->meetingAttendeeBelongsToTeamFinder, $this->attendeeRepository, $this->dispatcher);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->teamId, $this->meetingId, $this->attendeeId);
    }
    public function test_execute_inviteCoordinatorToAttendMeeting()
    {
        $this->teamMember->expects($this->once())
                ->method("cancelInvitation")
                ->with($this->meetingAttendeeBelongsToTeamFinder, $this->meetingId, $this->attendee);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->teamMemberRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
    public function test_execute_dispatchAttendee()
    {
        $this->dispatcher->expects($this->once())
                ->method("dispatch")
                ->with($this->attendee);
        $this->execute();
    }
}
