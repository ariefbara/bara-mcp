<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession;

class ConsultationSessionView
{

    /**
     *
     * @var ConsultationSessionRepository
     */
    protected $consultationSessionRepository;

    function __construct(ConsultationSessionRepository $consultationSessionRepository)
    {
        $this->consultationSessionRepository = $consultationSessionRepository;
    }

    public function showById(
            ProgramConsultantCompositionId $programConsultantCompositionId, string $consultationSessionId): ConsultationSession
    {
        return $this->consultationSessionRepository->ofId($programConsultantCompositionId, $consultationSessionId);
    }

    /**
     * 
     * @param ProgramConsultantCompositionId $programConsultantCompositionId
     * @param int $page
     * @param int $pageSize
     * @return ConsultationSession[]
     */
    public function showAll(ProgramConsultantCompositionId $programConsultantCompositionId, int $page, int $pageSize)
    {
        return $this->consultationSessionRepository->all($programConsultantCompositionId, $page, $pageSize);
    }

}
