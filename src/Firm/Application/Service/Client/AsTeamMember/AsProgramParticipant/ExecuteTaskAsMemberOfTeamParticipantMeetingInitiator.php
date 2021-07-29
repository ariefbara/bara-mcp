<?php

namespace Firm\Application\Service\Client\AsTeamMember\AsProgramParticipant;

use Firm\Application\Service\Client\AsTeamMember\TeamMemberRepository;
use Firm\Application\Service\Firm\Program\Participant\ParticipantAttendeeRepository;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\ITaskExecutableByMeetingInitiator;

class ExecuteTaskAsMemberOfTeamParticipantMeetingInitiator
{

    /**
     * 
     * @var TeamMemberRepository
     */
    protected $teamMemberRepository;

    /**
     * 
     * @var TeamParticipantRepository
     */
    protected $teamParticipantRepository;

    /**
     * 
     * @var ParticipantAttendeeRepository
     */
    protected $participantAttendeeRepository;

    public function __construct(
            TeamMemberRepository $teamMemberRepository, TeamParticipantRepository $teamParticipantRepository,
            ParticipantAttendeeRepository $participantAttendeeRepository)
    {
        $this->teamMemberRepository = $teamMemberRepository;
        $this->teamParticipantRepository = $teamParticipantRepository;
        $this->participantAttendeeRepository = $participantAttendeeRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $teamId, string $participantId, string $participantAttendeeId,
            ITaskExecutableByMeetingInitiator $task): void
    {
        $teamParticipant = $this->teamParticipantRepository->ofId($participantId);
        $participantAttendee = $this->participantAttendeeRepository->ofId($participantAttendeeId);
        $this->teamMemberRepository->aTeamMemberCorrespondWithTeam($firmId, $clientId, $teamId)
                ->executeTaskAsMemberOfTeamParticipantMeetingInitiator($teamParticipant, $participantAttendee, $task);
        
        $this->teamMemberRepository->update();
    }

}
