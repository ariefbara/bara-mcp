<?php

namespace Participant\Application\Service\ClientParticipant;

use Participant\Application\Service\ClientParticipantRepository;
use Participant\Domain\Model\Participant\ConsultationRequestData;
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
            string $firmId, string $clientId, string $programParticipationId, string $consultationRequestId,
            ConsultationRequestData $consultationRequestData): void
    {
        $clientParticipant = $this->clientParticipantRepository->ofId($firmId, $clientId, $programParticipationId);
        $clientParticipant->reproposeConsultationRequest($consultationRequestId, $consultationRequestData);
        $this->clientParticipantRepository->update();

        $this->dispatcher->dispatch($clientParticipant);
    }

}
