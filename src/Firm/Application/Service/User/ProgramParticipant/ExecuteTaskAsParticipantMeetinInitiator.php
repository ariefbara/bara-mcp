<?php

namespace Firm\Application\Service\User\ProgramParticipant;

use Firm\Application\Service\Firm\Program\Participant\ParticipantAttendeeRepository;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\ITaskExecutableByMeetingInitiator;

class ExecuteTaskAsParticipantMeetinInitiator
{

    /**
     * 
     * @var UserParticipantRepository
     */
    protected $userParticipantRepository;

    /**
     * 
     * @var ParticipantAttendeeRepository
     */
    protected $participantAttendeeRepository;

    public function __construct(UserParticipantRepository $userParticipantRepository,
            ParticipantAttendeeRepository $participantAttendeeRepository)
    {
        $this->userParticipantRepository = $userParticipantRepository;
        $this->participantAttendeeRepository = $participantAttendeeRepository;
    }

    public function execute(
            string $userId, string $participantId, string $participantAttendeeId, ITaskExecutableByMeetingInitiator $task): void
    {
        $participantAttendee = $this->participantAttendeeRepository->ofId($participantAttendeeId);
        $this->userParticipantRepository->aUserParticipantBelongsToUser($userId, $participantId)
                ->executeTaskAsParticipantMeetinInitiator($participantAttendee, $task);
        $this->userParticipantRepository->update();
    }

}
