<?php

namespace Firm\Application\Service\Client\AsTeamMember\AsProgramParticipant;

use Firm\ {
    Application\Service\Client\AsTeamMember\AsProgramParticipant\TeamParticipantRepository,
    Application\Service\Client\AsTeamMember\TeamMemberRepository,
    Application\Service\Firm\Program\ActivityTypeRepository,
    Application\Service\Firm\Program\MeetingType\MeetingRepository,
    Domain\Model\Firm\Program\MeetingType\MeetingData
};

class InitiateMeeting
{

    /**
     *
     * @var MeetingRepository
     */
    protected $meetingRepository;

    /**
     *
     * @var TeamMemberRepository
     */
    protected $teamMemberRepository;

    /**
     *
     * @var TeamParticipantRepository
     */
    protected $teamParticipantRepository;

    /**
     *
     * @var ActivityTypeRepository
     */
    protected $activityTypeRepository;

    function __construct(
            MeetingRepository $meetingRepository, TeamMemberRepository $teamMemberRepository,
            TeamParticipantRepository $teamParticipantRepository, ActivityTypeRepository $activityTypeRepository)
    {
        $this->meetingRepository = $meetingRepository;
        $this->teamMemberRepository = $teamMemberRepository;
        $this->teamParticipantRepository = $teamParticipantRepository;
        $this->activityTypeRepository = $activityTypeRepository;
    }

    public function execute(string $firmId, string $clientId, string $teamId, string $programId, string $activityTypeId,
            MeetingData $meetingData): string
    {
        $id = $this->meetingRepository->nextIdentity();
        $teamParticipant = $this->teamParticipantRepository->aTeamParticipantCorrespondWitnProgram($teamId, $programId);
        $meetingType = $this->activityTypeRepository->ofId($activityTypeId);
        $meeting = $this->teamMemberRepository->aTeamMemberCorrespondWithTeam($firmId, $clientId, $teamId)
                ->initiateMeeting($id, $teamParticipant, $meetingType, $meetingData);
        $this->meetingRepository->add($meeting);
        return $id;
    }

}
