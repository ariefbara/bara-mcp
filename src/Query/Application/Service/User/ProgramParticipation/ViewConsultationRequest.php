<?php

namespace Query\Application\Service\User\ProgramParticipation;

use Query\ {
    Application\Service\Firm\Program\ConsulationSetup\ConsultationRequestFilter,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest
};

class ViewConsultationRequest
{
    /**
     *
     * @var ConsultationRequestRepository
     */
    protected $consultationRequestRepository;
    
    public function __construct(ConsultationRequestRepository $consultationRequestRepository)
    {
        $this->consultationRequestRepository = $consultationRequestRepository;
    }
    
    /**
     * 
     * @param string $userId
     * @param string $userParticipantId
     * @param int $page
     * @param int $pageSize
     * @param ConsultationRequestFilter|null $consultationRequestFilter
     * @return ConsultationRequest[]
     */
    public function showAll(string $userId, string $userParticipantId, int $page, int $pageSize, ?ConsultationRequestFilter $consultationRequestFilter)
    {
        return $this->consultationRequestRepository->allConsultationRequestFromUserParticipant($userId, $userParticipantId, $page, $pageSize, $consultationRequestFilter);
    }
    
    public function showById(string $userId, string $userParticipantId, string $consultationRequestId): ConsultationRequest
    {
        return $this->consultationRequestRepository->aConsultationRequestFromUserParticipant($userId, $userParticipantId, $consultationRequestId);
    }

}
