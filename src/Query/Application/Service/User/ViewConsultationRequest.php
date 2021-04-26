<?php

namespace Query\Application\Service\User;

use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest;
use Query\Infrastructure\QueryFilter\ConsultationRequestFilter;

class ViewConsultationRequest
{

    /**
     * 
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * 
     * @var ConsultationRequestRepository
     */
    protected $consultationRequestRepository;

    public function __construct(UserRepository $userRepository,
            ConsultationRequestRepository $consultationRequestRepository)
    {
        $this->userRepository = $userRepository;
        $this->consultationRequestRepository = $consultationRequestRepository;
    }

    /**
     * 
     * @param string $userId
     * @param int $page
     * @param int $pageSize
     * @param ConsultationRequestFilter|null $consultationRequestFilter
     * @return ConsultationRequest[]
     */
    public function showAll(
            string $userId, int $page, int $pageSize, ?ConsultationRequestFilter $consultationRequestFilter)
    {
        return $this->userRepository->ofId($userId)
                        ->viewAllConsultationRequests(
                                $this->consultationRequestRepository, $page, $pageSize, $consultationRequestFilter);
    }

}
