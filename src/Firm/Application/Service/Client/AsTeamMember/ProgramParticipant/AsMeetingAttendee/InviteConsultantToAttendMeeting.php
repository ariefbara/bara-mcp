<?php

namespace Firm\Application\Service\Client\AsTeamMember\ProgramParticipant\AsMeetingAttendee;

use Firm\{
    Application\Service\Client\AsTeamMember\TeamMemberRepository,
    Application\Service\Firm\Program\ConsultantRepository,
    Domain\Service\MeetingAttendeeBelongsToTeamFinder
};

class InviteConsultantToAttendMeeting
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
     * @var ConsultantRepository
     */
    protected $consultantRepository;

    function __construct(
            TeamMemberRepository $teamMemberRepository,
            MeetingAttendeeBelongsToTeamFinder $meetingAttendeeBelongsToTeamFinder,
            ConsultantRepository $consultantRepository)
    {
        $this->teamMemberRepository = $teamMemberRepository;
        $this->meetingAttendeeBelongsToTeamFinder = $meetingAttendeeBelongsToTeamFinder;
        $this->consultantRepository = $consultantRepository;
    }

    public function execute(string $firmId, string $clientId, string $teamId, string $meetingId, string $consultantId): void
    {
        $consultant = $this->consultantRepository->aConsultantOfId($consultantId);
        $this->teamMemberRepository->aTeamMemberCorrespondWithTeam($firmId, $clientId, $teamId)
                ->inviteUserToAttendMeeting($this->meetingAttendeeBelongsToTeamFinder, $meetingId, $consultant);
        $this->teamMemberRepository->update();
    }

}
