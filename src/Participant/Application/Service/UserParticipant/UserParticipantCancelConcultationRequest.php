<?php

namespace Participant\Application\Service\UserParticipant;

use Participant\Application\Service\Participant\ConsultationRequestRepository;
use Resources\Application\Event\Dispatcher;

class UserParticipantCancelConcultationRequest
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
            string $userId, string $userParticipantId, string $consultationRequestId): void
    {
        $consultationRequest = $this->consultationRequestRepository
                ->aConsultationRequestFromUserParticipant($userId, $userParticipantId, $consultationRequestId);
        $consultationRequest->cancel();
        $this->consultationRequestRepository->update();
        
        $this->dispatcher->dispatch($consultationRequest);
    }

}
