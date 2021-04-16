<?php

namespace Query\Application\Service\Client;

use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession;
use Query\Infrastructure\QueryFilter\ConsultationSessionFilter;

class ViewConsultationSession
{

    /**
     * 
     * @var ClientRepository
     */
    protected $clientRepository;

    /**
     * 
     * @var ConsultationSessionRepository
     */
    protected $consultationSessionRepository;

    public function __construct(ClientRepository $clientRepository,
            ConsultationSessionRepository $consultationSessionRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->consultationSessionRepository = $consultationSessionRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $clientId
     * @param int $page
     * @param int $pageSize
     * @param ConsultationSessionFilter|null $consultationSessionFilter
     * @return ConsultationSession[]
     */
    public function showAll(
            string $firmId, string $clientId, int $page, int $pageSize,
            ?ConsultationSessionFilter $consultationSessionFilter)
    {
        return $this->clientRepository->aClientInFirm($firmId, $clientId)
                        ->viewAllAccessibleConsultationSessions(
                                $this->consultationSessionRepository, $page, $pageSize, $consultationSessionFilter);
    }

}
