<?php

namespace Firm\Application\Service\Client\AsTeamMember\ProgramParticipant\AsMeetingAttendee;

use Firm\{
    Application\Service\Client\AsTeamMember\TeamMemberRepository,
    Application\Service\Firm\Program\MeetingType\Meeting\AttendeeRepository,
    Domain\Service\MeetingAttendeeBelongsToTeamFinder
};

class CancelInvitation
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

    /**
     *
     * @var AttendeeRepository
     */
    protected $attendeeRepository;

    function __construct(
            TeamMemberRepository $teamMemberRepository,
            MeetingAttendeeBelongsToTeamFinder $meetingAttendeeBelongsToTeamFinder,
            AttendeeRepository $attendeeRepository)
    {
        $this->teamMemberRepository = $teamMemberRepository;
        $this->meetingAttendeeBelongsToTeamFinder = $meetingAttendeeBelongsToTeamFinder;
        $this->attendeeRepository = $attendeeRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $teamId, string $meetingId, string $attendeeId): void
    {
        $attendee = $this->attendeeRepository->ofId($attendeeId);
        $this->teamMemberRepository->aTeamMemberCorrespondWithTeam($firmId, $clientId, $teamId)
                ->cancelInvitation($this->meetingAttendeeBelongsToTeamFinder, $meetingId, $attendee);
        $this->teamMemberRepository->update();
    }

}
