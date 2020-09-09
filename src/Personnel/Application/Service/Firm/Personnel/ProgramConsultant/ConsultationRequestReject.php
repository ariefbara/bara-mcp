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
            string $firmId, string $personnelId, string $programConsultantId, string $consultationRequestId): void
    {
        $this->consultationRequestRepository->ofId($firmId, $personnelId, $programConsultantId, $consultationRequestId)
                ->reject();
        $this->consultationRequestRepository->update();
    }

}
