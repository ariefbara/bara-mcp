<?php

namespace Query\Application\Service\Firm\Program;

use Query\{
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession,
    Infrastructure\QueryFilter\ConsultationSessionFilter
};

class ViewConsultationSession
{

    /**
     *
     * @var ConsultationSessionRepository
     */
    protected $consultationSessionRepository;

    public function __construct(ConsultationSessionRepository $consultationSessionRepository)
    {
        $this->consultationSessionRepository = $consultationSessionRepository;
    }

    /**
     * 
     * @param string $programId
     * @param int $page
     * @param int $pageSize
     * @param ConsultationSessionFilter|null $consultationSessionFilter
     * @return ConsultationSession[]
     */
    public function showAll(
            string $programId, int $page, int $pageSize, ?ConsultationSessionFilter $consultationSessionFilter)
    {
        return $this->consultationSessionRepository
                        ->allConsultationSessionsInProgram($programId, $page, $pageSize, $consultationSessionFilter);
    }

    public function showById(string $programId, string $consultationSessionId): ConsultationSession
    {
        return $this->consultationSessionRepository->aConsultationSessionInProgram($programId, $consultationSessionId);
    }

}
