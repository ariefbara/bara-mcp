<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant;

class ConsultationRequestReject
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
            ProgramConsultantCompositionId $programConsultantCompositionId, string $consultationRequestId): void
    {
        $this->consultationRequestRepository->ofId($programConsultantCompositionId, $consultationRequestId)
                ->reject();
        $this->consultationRequestRepository->update();
    }

}
