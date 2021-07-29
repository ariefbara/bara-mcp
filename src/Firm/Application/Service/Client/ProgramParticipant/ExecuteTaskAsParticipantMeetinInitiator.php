<?php

namespace Firm\Application\Service\Client\ProgramParticipant;

use Firm\Application\Service\Firm\Program\Participant\ParticipantAttendeeRepository;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\ITaskExecutableByMeetingInitiator;

class ExecuteTaskAsParticipantMeetinInitiator
{

    /**
     * 
     * @var ClientParticipantRepository
     */
    protected $clientParticipantRepository;

    /**
     * 
     * @var ParticipantAttendeeRepository
     */
    protected $participantAttendeeRepository;

    public function __construct(ClientParticipantRepository $clientParticipantRepository,
            ParticipantAttendeeRepository $participantAttendeeRepository)
    {
        $this->clientParticipantRepository = $clientParticipantRepository;
        $this->participantAttendeeRepository = $participantAttendeeRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $participantId, string $participantAttendeeId,
            ITaskExecutableByMeetingInitiator $task): void
    {
        $participantAttendee = $this->participantAttendeeRepository->ofId($participantAttendeeId);
        $this->clientParticipantRepository->aClientParticipantBelongsToClient($firmId, $clientId, $participantId)
                ->executeTaskAsParticipantMeetinInitiator($participantAttendee, $task);
        $this->clientParticipantRepository->update();
    }

}
