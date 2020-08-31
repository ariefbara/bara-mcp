<?php

namespace Participant\Application\Service\Participant;

use DateTimeImmutable;
use Participant\Application\Service\ClientParticipantRepository;
use Resources\Application\Event\Dispatcher;

class ClientChangeConsultationRequestTime
{

    /**
     *
     * @var ClientParticipantRepository
     */
    protected $clientParticipantRepository;

    /**
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    function __construct(ClientParticipantRepository $clientParticipantRepository, Dispatcher $dispatcher)
    {
        $this->clientParticipantRepository = $clientParticipantRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(
            string $firmId, string $clientId, string $programId, string $consultationRequestId,
            DateTimeImmutable $startTime): void
    {
        $clientParticipant = $this->clientParticipantRepository->ofId($firmId, $clientId, $programId);
        $clientParticipant->reproposeConsultationRequest($consultationRequestId, $startTime);
        $this->clientParticipantRepository->update();

        $this->dispatcher->dispatch($clientParticipant);
    }

}
