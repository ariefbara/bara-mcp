<?php

namespace Firm\Application\Service\Client\AsTeamMember\ProgramParticipant\AsMeetingAttendee;

use Firm\Application\Service\Client\AsTeamMember\TeamMemberRepository;
use Firm\Application\Service\Firm\Program\ParticipantRepository;
use Firm\Domain\Service\MeetingAttendeeBelongsToTeamFinder;
use Resources\Application\Event\Dispatcher;

class InviteParticipantToAttendMeeting
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
     * @var ParticipantRepository
     */
    protected $participantRepository;

    /**
     * 
     * @var Dispatcher
     */
    protected $dispatcher;

    function __construct(
            TeamMemberRepository $teamMemberRepository,
            MeetingAttendeeBelongsToTeamFinder $meetingAttendeeBelongsToTeamFinder,
            ParticipantRepository $participantRepository, Dispatcher $dispatcher)
    {
        $this->teamMemberRepository = $teamMemberRepository;
        $this->meetingAttendeeBelongsToTeamFinder = $meetingAttendeeBelongsToTeamFinder;
        $this->participantRepository = $participantRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(string $firmId, string $clientId, string $teamId, string $meetingId, string $participantId): void
    {
        $participant = $this->participantRepository->ofId($participantId);
        $teamMember = $this->teamMemberRepository->aTeamMemberCorrespondWithTeam($firmId, $clientId, $teamId);
        $teamMember->inviteUserToAttendMeeting($this->meetingAttendeeBelongsToTeamFinder, $meetingId, $participant);
        $this->teamMemberRepository->update();
        
        $this->dispatcher->dispatch($teamMember);
    }

}
