<?php

namespace Client\Application\Service\Client\ProgramParticipation;

class ConsultationRequestCancel
{

    /**
     *
     * @var ConsultationRequestRepository
     */
    protected $consultationRequestRepository;

    function __construct(ConsultationRequestRepository $consultationRequestRepository)
    {
        $this->consultationRequestRepository = $consultationRequestRepository;
    }

    public function execute(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $consultationRequestId): void
    {
        $this->consultationRequestRepository
                ->ofId($programParticipationCompositionId, $consultationRequestId)
                ->cancel();
        $this->consultationRequestRepository->update();
    }

}
