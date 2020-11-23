<?php

namespace Firm\Application\Service\Client\AsTeamMember\ProgramParticipant\AsMeetingAttendee;

use Firm\{
    Application\Service\Client\AsTeamMember\TeamMemberRepository,
    Domain\Model\Firm\Program\MeetingType\MeetingData,
    Domain\Service\MeetingAttendeeBelongsToTeamFinder
};

class UpdateMeeting
{

    /**
     *
     * @var TeamMemberRepository
     */
    protected $teamMemberRepository;

    /**
     *
     * @var MeetingAttendeeBelongsToTeamFinder
     */
    protected $meetingAttendeeBelongsToTeamFinder;

    function __construct(TeamMemberRepository $teamMemberRepository,
            MeetingAttendeeBelongsToTeamFinder $meetingAttendeeBelongsToTeamFinder)
    {
        $this->teamMemberRepository = $teamMemberRepository;
        $this->meetingAttendeeBelongsToTeamFinder = $meetingAttendeeBelongsToTeamFinder;
    }

    public function execute(
            string $firmId, string $clientId, string $teamId, string $meetingId, MeetingData $meetingData): void
    {
        $this->teamMemberRepository->aTeamMemberCorrespondWithTeam($firmId, $clientId, $teamId)
                ->updateMeeting($this->meetingAttendeeBelongsToTeamFinder, $meetingId, $meetingData);
        $this->teamMemberRepository->update();
    }

}
