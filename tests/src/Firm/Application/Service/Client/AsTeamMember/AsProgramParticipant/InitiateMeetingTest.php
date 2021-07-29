<?php

namespace Firm\Application\Service\Client\AsTeamMember\AsProgramParticipant;

use Firm\Application\Service\Client\AsTeamMember\TeamMemberRepository;
use Firm\Application\Service\Firm\Program\ActivityType\MeetingRepository;
use Firm\Application\Service\Firm\Program\ActivityTypeRepository;
use Firm\Domain\Model\Firm\Program\ActivityType;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;
use Firm\Domain\Model\Firm\Program\ActivityType\MeetingData;
use Firm\Domain\Model\Firm\Program\TeamParticipant;
use Firm\Domain\Model\Firm\Team\Member;
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class InitiateMeetingTest extends TestBase
{

    protected $meetingRepository, $nextId = "nextId";
    protected $teamMemberRepository, $teamMember;
    protected $teamParticipantRepository, $teamParticipant;
    protected $activityTypeRepository, $activityType;
    protected $dispatcher;
    protected $service;
    protected $firmId = "firmId", $clientId = "clientId", $teamId = "teamId", $programId = "programId",
            $meetingTypeId = "meetingTypeId";
    protected $meetingData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->meetingRepository = $this->buildMockOfInterface(MeetingRepository::class);
        $this->meetingRepository->expects($this->any())
                ->method("nextIdentity")
                ->willReturn($this->nextId);

        $this->teamMember = $this->buildMockOfClass(Member::class);
        $this->teamMemberRepository = $this->buildMockOfInterface(TeamMemberRepository::class);
        $this->teamMemberRepository->expects($this->any())
                ->method("aTeamMemberCorrespondWithTeam")
                ->with($this->firmId, $this->clientId, $this->teamId)
                ->willReturn($this->teamMember);

        $this->teamParticipant = $this->buildMockOfClass(TeamParticipant::class);
        $this->teamParticipantRepository = $this->buildMockOfInterface(TeamParticipantRepository::class);
        $this->teamParticipantRepository->expects($this->any())
                ->method("aTeamParticipantCorrespondWitnProgram")
                ->with($this->teamId, $this->programId)
                ->willReturn($this->teamParticipant);

        $this->activityType = $this->buildMockOfClass(ActivityType::class);
        $this->activityTypeRepository = $this->buildMockOfInterface(ActivityTypeRepository::class);
        $this->activityTypeRepository->expects($this->any())
                ->method("ofId")
                ->with($this->meetingTypeId)
                ->willReturn($this->activityType);

        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);

        $this->service = new InitiateMeeting(
                $this->meetingRepository, $this->teamMemberRepository, $this->teamParticipantRepository,
                $this->activityTypeRepository, $this->dispatcher);

        $this->meetingData = $this->buildMockOfClass(MeetingData::class);
    }

    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->clientId, $this->teamId, $this->programId,
                        $this->meetingTypeId, $this->meetingData);
    }

    public function test_execute_addMeetingToRepository()
    {
        $this->teamMember->expects($this->once())
                ->method("initiateMeeting")
                ->with($this->nextId, $this->teamParticipant, $this->activityType, $this->meetingData);
        $this->meetingRepository->expects($this->once())
                ->method("add");
        $this->execute();
    }

    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }
    public function test_execute_dispatcheMeeting()
    {
        $meeting = $this->buildMockOfClass(Meeting::class);
        $this->teamMember->expects($this->once())
                ->method("initiateMeeting")
                ->willReturn($meeting);
        $this->dispatcher->expects($this->once())
                ->method("dispatch")
                ->with($meeting);
        $this->execute();
    }

}
