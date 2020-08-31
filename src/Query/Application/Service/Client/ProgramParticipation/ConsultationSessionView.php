<?php

namespace Query\Application\Service\Client\ProgramParticipation;

use Query\ {
    Application\Service\Firm\Program\ConsulationSetup\ConsultationSessionFilter,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession
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
     * @param string $clientId
     * @param string $programParticipationId
     * @param int $page
     * @param int $pageSize
     * @param ConsultationSessionFilter|null $consultationSessionFilter
     * @return ConsultationSession[]
     */
    public function showAll(string $clientId, string $programParticipationId, int $page, int $pageSize,
            ?ConsultationSessionFilter $consultationSessionFilter)
    {
        return $this->consultationSessionRepository->allConsultationSessionsOfClient($clientId, $programParticipationId,
                        $page, $pageSize, $consultationSessionFilter);
    }

    public function showById(string $clientId, string $programParticipationId, string $consultationSessionId): ConsultationSession
    {
        return $this->consultationSessionRepository->aConsultationSessionOfClient($clientId, $programParticipationId,
                        $consultationSessionId);
    }

}
