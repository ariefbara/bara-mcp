<?php

namespace Firm\Application\Service\Client\AsTeamMember\ProgramParticipant\AsMeetingAttendee;

use Firm\Application\Service\Client\AsTeamMember\TeamMemberRepository;
use Firm\Domain\Model\Firm\Program\MeetingType\MeetingData;
use Firm\Domain\Model\Firm\Team\Member;
use Firm\Domain\Service\MeetingAttendeeBelongsToTeamFinder;
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class UpdateMeetingTest extends TestBase
{
    protected $teamMemberRepository, $teamMember;
    protected $meetingAttendeeBelongsToTeamFinder;
    protected $dispatcher;
    protected $service;
    protected $firmId = "firmId", $clientId = "clientId", $teamId = "teamId", $meetingId = "meetingId";
    protected $meetingData;

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
        
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);
        
        $this->service = new UpdateMeeting($this->teamMemberRepository, $this->meetingAttendeeBelongsToTeamFinder, $this->dispatcher);
        
        $this->meetingData = $this->buildMockOfClass(MeetingData::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->teamId, $this->meetingId, $this->meetingData);
    }
    public function test_execute_executeAttendeesUpdateMeeting()
    {
        $this->teamMember->expects($this->once())
                ->method("updateMeeting")
                ->with($this->meetingAttendeeBelongsToTeamFinder, $this->meetingId, $this->meetingData);
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
