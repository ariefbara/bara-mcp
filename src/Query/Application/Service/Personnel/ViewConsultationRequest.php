<?php

namespace Query\Application\Service\Personnel;

use Query\Infrastructure\QueryFilter\ConsultationRequestFilter;

class ViewConsultationRequest
{

    /**
     * 
     * @var PersonnelRepository
     */
    protected $personnelRepository;

    /**
     * 
     * @var ConsultationRequestRepository
     */
    protected $consultationRequestRepository;

    public function __construct(
            PersonnelRepository $personnelRepository, ConsultationRequestRepository $consultationRequestRepository)
    {
        $this->personnelRepository = $personnelRepository;
        $this->consultationRequestRepository = $consultationRequestRepository;
    }

    public function showAll(
            string $firmId, string $personnelId, int $page, int $pageSize,
            ?ConsultationRequestFilter $consultationRequestFilter)
    {
        return $this->personnelRepository
                        ->aPersonnelInFirm($firmId, $personnelId)
                        ->viewAllConsultationRequests(
                                $this->consultationRequestRepository, $page, $pageSize, $consultationRequestFilter);
    }

}
