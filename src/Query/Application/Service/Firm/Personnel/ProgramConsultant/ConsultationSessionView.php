<?php

namespace Query\Application\Service\Firm\Personnel\ProgramConsultant;

use Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ProgramConsultantCompositionId;
use Query\Domain\Model\Firm\Program\Participant\ConsultationSession;

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

    /**
     * 
     * @param ProgramConsultantCompositionId $programConsultantCompositionId
     * @param int $page
     * @param int $pageSize
     * @return ConsultationSession[]
     */
    public function showAll(ProgramConsultantCompositionId $programConsultantCompositionId, int $page, int $pageSize)
    {
        return $this->consultationSessionRepository->allConsultationSessionsOfPersonnel(
                        $programConsultantCompositionId, $page, $pageSize);
    }

    public function showById(ProgramConsultantCompositionId $programConsultantCompositionId,
            string $consultationSessionId): ConsultationSession
    {
        return $this->consultationSessionRepository->aConsultationSessionOfPersonnel(
                        $programConsultantCompositionId, $consultationSessionId);
    }

}
