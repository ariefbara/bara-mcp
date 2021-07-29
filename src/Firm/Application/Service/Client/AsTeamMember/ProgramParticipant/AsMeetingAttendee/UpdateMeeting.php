<?php

namespace Firm\Application\Service\Client\AsTeamMember\ProgramParticipant\AsMeetingAttendee;

use Firm\Application\Service\Client\AsTeamMember\TeamMemberRepository;
use Firm\Domain\Model\Firm\Program\ActivityType\MeetingType\MeetingData;
use Firm\Domain\Service\MeetingAttendeeBelongsToTeamFinder;
use Resources\Application\Event\Dispatcher;

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

    /**
     * 
     * @var Dispatcher
     */
    protected $dispatcher;

    function __construct(
            TeamMemberRepository $teamMemberRepository,
            MeetingAttendeeBelongsToTeamFinder $meetingAttendeeBelongsToTeamFinder, Dispatcher $dispatcher)
    {
        $this->teamMemberRepository = $teamMemberRepository;
        $this->meetingAttendeeBelongsToTeamFinder = $meetingAttendeeBelongsToTeamFinder;
        $this->dispatcher = $dispatcher;
    }

    public function execute(
            string $firmId, string $clientId, string $teamId, string $meetingId, MeetingData $meetingData): void
    {
        $teamMember = $this->teamMemberRepository->aTeamMemberCorrespondWithTeam($firmId, $clientId, $teamId);
        $teamMember->updateMeeting($this->meetingAttendeeBelongsToTeamFinder, $meetingId, $meetingData);
        $this->teamMemberRepository->update();
        
        $this->dispatcher->dispatch($teamMember);
    }

}
