<?php

namespace Client\Application\Service\Client\ProgramParticipation;

use Client\Application\Service\Client\ProgramParticipationRepository;
use Resources\Application\Event\Dispatcher;

class ConsultationRequestAccept
{

    /**
     *
     * @var ProgramParticipationRepository
     */
    protected $programParticipationRepository;

    /**
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    function __construct(ProgramParticipationRepository $programParticipationRepository, Dispatcher $dispatcher)
    {
        $this->programParticipationRepository = $programParticipationRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(
            string $clientId, string $programParticipationId, string $consultationRequestId): void
    {
        $programParticipation = $this->programParticipationRepository->ofId($clientId, $programParticipationId);
        $programParticipation->acceptConsultationRequest($consultationRequestId);
        $this->programParticipationRepository->update();
        
        $this->dispatcher->dispatch($programParticipation);
    }

}
