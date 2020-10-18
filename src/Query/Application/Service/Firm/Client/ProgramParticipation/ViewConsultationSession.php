<?php

namespace Query\Application\Service\Firm\Client\ProgramParticipation;

use Query\ {
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
     * @param string $firmId
     * @param string $clientId
     * @param string $programParticipationId
     * @param int $page
     * @param int $pageSize
     * @return ConsultationSession[]
     */
    public function showAll(
            string $firmId, string $clientId, string $programParticipationId, int $page, int $pageSize,
            ConsultationSessionFilter $consultationSessionFilter)
    {
        return $this->consultationSessionRepository->allConsultationsSessionOfClient(
                        $firmId, $clientId, $programParticipationId, $page, $pageSize, $consultationSessionFilter);
    }

    public function showById(string $firmId, string $clientId, string $programParticipationId, string $consultationSessionId): ConsultationSession
    {
        return $this->consultationSessionRepository
                        ->aConsultationSessionOfClient($firmId, $clientId, $programParticipationId, $consultationSessionId);
    }

}
