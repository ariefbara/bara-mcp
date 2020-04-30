<?php

namespace Client\Application\Service\Client\ProgramParticipation;

use Client\Application\Service\Client\ProgramParticipationRepository;

class ParticipantNotificationAdd
{

    /**
     *
     * @var ParticipantNotificationRepository
     */
    protected $participantNotificationRepository;

    /**
     *
     * @var ProgramParticipationRepository
     */
    protected $programParticipationRepository;

    function __construct(
            ParticipantNotificationRepository $participantNotificationRepository,
            ProgramParticipationRepository $programParticipationRepository)
    {
        $this->participantNotificationRepository = $participantNotificationRepository;
        $this->programParticipationRepository = $programParticipationRepository;
    }

    public function execute(string $firmId, string $programId, string $participantId, string $message): void
    {
        $id = $this->participantNotificationRepository->nextIdentity();
        $participantNotification = $this->programParticipationRepository
                ->aProgramParticipationOfProgram($firmId, $programId, $participantId)
                ->createParticipantNotification($id, $message);
        $this->participantNotificationRepository->add($participantNotification);
    }

}
