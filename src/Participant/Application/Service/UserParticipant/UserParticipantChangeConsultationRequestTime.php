<?php

namespace Participant\Application\Service\UserParticipant;

use DateTimeImmutable;
use Participant\Application\Service\UserParticipantRepository;
use Resources\Application\Event\Dispatcher;

class UserParticipantChangeConsultationRequestTime
{

    /**
     *
     * @var UserParticipantRepository
     */
    protected $userParticipantRepository;

    /**
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    function __construct(UserParticipantRepository $userParticipantRepository, Dispatcher $dispatcher)
    {
        $this->userParticipantRepository = $userParticipantRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(
            string $userId, string $userParticipantId, string $consultationRequestId,
            DateTimeImmutable $startTime): void
    {
        $userParticipant = $this->userParticipantRepository->ofId($userId, $userParticipantId);
        $userParticipant->reproposeConsultationRequest($consultationRequestId, $startTime);
        $this->userParticipantRepository->update();

        $this->dispatcher->dispatch($userParticipant);
    }

}
