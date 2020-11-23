<?php

namespace Firm\Application\Service\Client\AsTeamMember\ProgramParticipant\AsMeetingAttendee;

use Firm\ {
    Application\Service\Client\AsTeamMember\TeamMemberRepository,
    Application\Service\Firm\ManagerRepository,
    Domain\Service\MeetingAttendeeBelongsToTeamFinder
};

class InviteManagerToAttendMeeting
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
     * @var ManagerRepository
     */
    protected $managerRepository;

    function __construct(
            TeamMemberRepository $teamMemberRepository,
            MeetingAttendeeBelongsToTeamFinder $meetingAttendeeBelongsToTeamFinder,
            ManagerRepository $managerRepository)
    {
        $this->teamMemberRepository = $teamMemberRepository;
        $this->meetingAttendeeBelongsToTeamFinder = $meetingAttendeeBelongsToTeamFinder;
        $this->managerRepository = $managerRepository;
    }

    public function execute(string $firmId, string $clientId, string $teamId, string $meetingId, string $managerId): void
    {
        $manager = $this->managerRepository->aManagerOfId($managerId);
        $this->teamMemberRepository->aTeamMemberCorrespondWithTeam($firmId, $clientId, $teamId)
                ->inviteUserToAttendMeeting($this->meetingAttendeeBelongsToTeamFinder, $meetingId, $manager);
        $this->teamMemberRepository->update();
    }

}
