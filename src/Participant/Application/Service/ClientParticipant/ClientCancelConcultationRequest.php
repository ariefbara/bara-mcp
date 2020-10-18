<?php

namespace Participant\Application\Service\ClientParticipant;

use Participant\Application\Service\Participant\ConsultationRequestRepository;
use Resources\Application\Event\Dispatcher;

class ClientCancelConcultationRequest
{

    /**
     *
     * @var ConsultationRequestRepository
     */
    protected $consultationRequestRepository;

    /**
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    public function __construct(ConsultationRequestRepository $consultationRequestRepository, Dispatcher $dispatcher)
    {
        $this->consultationRequestRepository = $consultationRequestRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(
            string $firmId, string $clientId, string $programId, string $consultationRequestId): void
    {
        $consultationRequest = $this->consultationRequestRepository
                ->aConsultationRequestFromClientParticipant($firmId, $clientId, $programId, $consultationRequestId);
        $consultationRequest->cancel();
        $this->consultationRequestRepository->update();
        
        $this->dispatcher->dispatch($consultationRequest);
    }

}
