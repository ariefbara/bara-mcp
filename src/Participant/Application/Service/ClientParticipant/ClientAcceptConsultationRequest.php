<?php

namespace Participant\Application\Service\ClientParticipant;

use Participant\Application\Service\ClientParticipantRepository;
use Resources\Application\Event\Dispatcher;

class ClientAcceptConsultationRequest
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
            string $firmId, string $clientId, string $programId, string $consultationRequestId): void
    {
        $clientParticipant = $this->clientParticipantRepository->ofId($firmId, $clientId, $programId);
        $clientParticipant->acceptConsultationRequest($consultationRequestId);
        $this->clientParticipantRepository->update();
        
        $this->dispatcher->dispatch($clientParticipant);
    }

}
