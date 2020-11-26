<?php

namespace Firm\Application\Service\Client\AsTeamMember\AsProgramParticipant;

use Firm\{
    Application\Service\Client\AsTeamMember\AsProgramParticipant\TeamParticipantRepository,
    Application\Service\Client\AsTeamMember\TeamMemberRepository,
    Application\Service\Firm\Program\ActivityTypeRepository,
    Application\Service\Firm\Program\MeetingType\MeetingRepository,
    Domain\Model\Firm\Program\ActivityType,
    Domain\Model\Firm\Program\MeetingType\MeetingData,
    Domain\Model\Firm\Program\TeamParticipant,
    Domain\Model\Firm\Team\Member
};
use Tests\TestBase;

class InitiateMeetingTest extends TestBase
{

    protected $meetingRepository, $nextId = "nextId";
    protected $teamMemberRepository, $teamMember;
    protected $teamParticipantRepository, $teamParticipant;
    protected $activityTypeRepository, $activityType;
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


        $this->service = new InitiateMeeting(
                $this->meetingRepository, $this->teamMemberRepository, $this->teamParticipantRepository,
                $this->activityTypeRepository);

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

}
