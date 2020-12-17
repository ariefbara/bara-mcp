<?php

namespace Firm\Application\Service\Client\AsTeamMember\ProgramParticipant\AsMeetingAttendee;

use Firm\Application\Service\Client\AsTeamMember\TeamMemberRepository;
use Firm\Application\Service\Firm\ManagerRepository;
use Firm\Domain\Service\MeetingAttendeeBelongsToTeamFinder;
use Resources\Application\Event\Dispatcher;

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

    /**
     * 
     * @var Dispatcher
     */
    protected $dispatcher;

    function __construct(
            TeamMemberRepository $teamMemberRepository,
            MeetingAttendeeBelongsToTeamFinder $meetingAttendeeBelongsToTeamFinder,
            ManagerRepository $managerRepository, Dispatcher $dispatcher)
    {
        $this->teamMemberRepository = $teamMemberRepository;
        $this->meetingAttendeeBelongsToTeamFinder = $meetingAttendeeBelongsToTeamFinder;
        $this->managerRepository = $managerRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(string $firmId, string $clientId, string $teamId, string $meetingId, string $managerId): void
    {
        $manager = $this->managerRepository->aManagerOfId($managerId);
        $teamMember = $this->teamMemberRepository->aTeamMemberCorrespondWithTeam($firmId, $clientId, $teamId);
        $teamMember->inviteUserToAttendMeeting($this->meetingAttendeeBelongsToTeamFinder, $meetingId, $manager);
        $this->teamMemberRepository->update();
        
        $this->dispatcher->dispatch($teamMember);
    }

}
