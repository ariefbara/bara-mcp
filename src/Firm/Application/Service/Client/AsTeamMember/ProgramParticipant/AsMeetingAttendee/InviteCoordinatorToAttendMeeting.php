<?php

namespace Firm\Application\Service\Client\AsTeamMember\ProgramParticipant\AsMeetingAttendee;

use Firm\Application\Service\Client\AsTeamMember\TeamMemberRepository;
use Firm\Application\Service\Firm\Program\CoordinatorRepository;
use Firm\Domain\Service\MeetingAttendeeBelongsToTeamFinder;
use Resources\Application\Event\Dispatcher;

class InviteCoordinatorToAttendMeeting
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
     * @var CoordinatorRepository
     */
    protected $coordinatorRepository;

    /**
     * 
     * @var Dispatcher
     */
    protected $dispatcher;

    function __construct(
            TeamMemberRepository $teamMemberRepository,
            MeetingAttendeeBelongsToTeamFinder $meetingAttendeeBelongsToTeamFinder,
            CoordinatorRepository $coordinatorRepository, Dispatcher $dispatcher)
    {
        $this->teamMemberRepository = $teamMemberRepository;
        $this->meetingAttendeeBelongsToTeamFinder = $meetingAttendeeBelongsToTeamFinder;
        $this->coordinatorRepository = $coordinatorRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(string $firmId, string $clientId, string $teamId, string $meetingId, string $coordinatorId): void
    {
        $coordinator = $this->coordinatorRepository->aCoordinatorOfId($coordinatorId);
        $teamMember = $this->teamMemberRepository->aTeamMemberCorrespondWithTeam($firmId, $clientId, $teamId);
        $teamMember->inviteUserToAttendMeeting($this->meetingAttendeeBelongsToTeamFinder, $meetingId, $coordinator);
        $this->teamMemberRepository->update();
        
        $this->dispatcher->dispatch($teamMember);
    }

}
