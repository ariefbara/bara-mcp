<?php

namespace Query\Application\Service\Client;

use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest;
use Query\Infrastructure\QueryFilter\ConsultationRequestFilter;

class ViewConsultationRequest
{

    /**
     * 
     * @var ClientRepository
     */
    protected $clientRepository;

    /**
     * 
     * @var ConsultationRequestRepository
     */
    protected $consultationRequestRepository;

    public function __construct(ClientRepository $clientRepository,
            ConsultationRequestRepository $consultationRequestRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->consultationRequestRepository = $consultationRequestRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $clientId
     * @param int $page
     * @param int $pageSize
     * @param ConsultationRequestFilter|null $consultationRequestFilter
     * @return ConsultationRequest[]
     */
    public function showAll(
            string $firmId, string $clientId, int $page, int $pageSize,
            ?ConsultationRequestFilter $consultationRequestFilter)
    {
        return $this->clientRepository->aClientInFirm($firmId, $clientId)
                        ->viewAllAccessibleConsultationRequest(
                                $this->consultationRequestRepository, $page, $pageSize, $consultationRequestFilter);
    }

}
