<?php

namespace Firm\Application\Service\Client\AsTeamMember\ProgramParticipant\AsMeetingAttendee;

use Firm\Application\Service\Client\AsTeamMember\TeamMemberRepository;
use Firm\Application\Service\Firm\Program\MeetingType\Meeting\AttendeeRepository;
use Firm\Domain\Service\MeetingAttendeeBelongsToTeamFinder;
use Resources\Application\Event\Dispatcher;

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

    /**
     * 
     * @var Dispatcher
     */
    protected $dispatcher;

    function __construct(
            TeamMemberRepository $teamMemberRepository,
            MeetingAttendeeBelongsToTeamFinder $meetingAttendeeBelongsToTeamFinder,
            AttendeeRepository $attendeeRepository, Dispatcher $dispatcher)
    {
        $this->teamMemberRepository = $teamMemberRepository;
        $this->meetingAttendeeBelongsToTeamFinder = $meetingAttendeeBelongsToTeamFinder;
        $this->attendeeRepository = $attendeeRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(
            string $firmId, string $clientId, string $teamId, string $meetingId, string $attendeeId): void
    {
        $attendee = $this->attendeeRepository->ofId($attendeeId);
        $this->teamMemberRepository->aTeamMemberCorrespondWithTeam($firmId, $clientId, $teamId)
                ->cancelInvitation($this->meetingAttendeeBelongsToTeamFinder, $meetingId, $attendee);
        $this->teamMemberRepository->update();
        
        $this->dispatcher->dispatch($attendee);
    }

}
