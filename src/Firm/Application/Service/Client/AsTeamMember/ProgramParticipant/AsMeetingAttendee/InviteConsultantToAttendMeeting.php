<?php

namespace Firm\Application\Service\Client\AsTeamMember\ProgramParticipant\AsMeetingAttendee;

use Firm\Application\Service\Client\AsTeamMember\TeamMemberRepository;
use Firm\Application\Service\Firm\Program\ConsultantRepository;
use Firm\Domain\Service\MeetingAttendeeBelongsToTeamFinder;
use Resources\Application\Event\Dispatcher;

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

    /**
     * 
     * @var Dispatcher
     */
    protected $dispatcher;

    function __construct(
            TeamMemberRepository $teamMemberRepository,
            MeetingAttendeeBelongsToTeamFinder $meetingAttendeeBelongsToTeamFinder,
            ConsultantRepository $consultantRepository, Dispatcher $dispatcher)
    {
        $this->teamMemberRepository = $teamMemberRepository;
        $this->meetingAttendeeBelongsToTeamFinder = $meetingAttendeeBelongsToTeamFinder;
        $this->consultantRepository = $consultantRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(string $firmId, string $clientId, string $teamId, string $meetingId, string $consultantId): void
    {
        $consultant = $this->consultantRepository->aConsultantOfId($consultantId);
        $teamMember = $this->teamMemberRepository->aTeamMemberCorrespondWithTeam($firmId, $clientId, $teamId);
        $teamMember->inviteUserToAttendMeeting($this->meetingAttendeeBelongsToTeamFinder, $meetingId, $consultant);
        $this->teamMemberRepository->update();
        
        $this->dispatcher->dispatch($teamMember);
    }

}
