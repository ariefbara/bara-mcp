<?php

namespace Client\Application\Service\Client\ProgramParticipation;

use Client\Application\Service\Client\ProgramParticipationRepository;
use DateTimeImmutable;
use Resources\Application\Event\Dispatcher;

class ConsultationRequestRepropose
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
            string $clientId, string $programParticipationId, string $consultationRequestId,
            DateTimeImmutable $startTime): void
    {
        $programParticipation = $this->programParticipationRepository->ofId($clientId, $programParticipationId);
        $programParticipation->reproposeConsultationRequest($consultationRequestId, $startTime);
        $this->programParticipationRepository->update();

        $this->dispatcher->dispatch($programParticipation);
    }

}
