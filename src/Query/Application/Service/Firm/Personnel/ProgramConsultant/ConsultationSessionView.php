<?php

namespace Query\Application\Service\Firm\Personnel\ProgramConsultant;

use Query\ {
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession,
    Infrastructure\QueryFilter\ConsultationSessionFilter
};

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
     * @param string $firmId
     * @param string $personnelId
     * @param string $consultantId
     * @param int $page
     * @param int $pageSize
     * @param ConsultationSessionFilter|null $consultationSessionFilter
     * @return ConsultationSession[]
     */
    public function showAll(string $firmId, string $personnelId, string $consultantId, int $page, int $pageSize,
            ?ConsultationSessionFilter $consultationSessionFilter)
    {
        return $this->consultationSessionRepository->allConsultationSessionsOfPersonnel($firmId, $personnelId,
                        $consultantId, $page, $pageSize, $consultationSessionFilter);
    }

    public function showById(string $firmId, string $personnelId, string $consultantId, string $consultationSessionId): ConsultationSession
    {
        return $this->consultationSessionRepository->aConsultationSessionOfPersonnel($firmId, $personnelId,
                        $consultantId, $consultationSessionId);
    }

}
