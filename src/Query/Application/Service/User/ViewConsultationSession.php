<?php

namespace Query\Application\Service\User;

use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession;
use Query\Infrastructure\QueryFilter\ConsultationSessionFilter;

class ViewConsultationSession
{

    /**
     * 
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * 
     * @var ConsultationSessionRepository
     */
    protected $consultationSessionRepository;

    public function __construct(UserRepository $userRepository,
            ConsultationSessionRepository $consultationSessionRepository)
    {
        $this->userRepository = $userRepository;
        $this->consultationSessionRepository = $consultationSessionRepository;
    }

    /**
     * 
     * @param string $userId
     * @param int $page
     * @param int $pageSize
     * @param ConsultationSessionFilter|null $consultationSessionFilter
     * @return ConsultationSession[]
     */
    public function showAll(string $userId, int $page, int $pageSize,
            ?ConsultationSessionFilter $consultationSessionFilter)
    {
        return $this->userRepository->ofId($userId)
                        ->viewAllConsultationSessions(
                                $this->consultationSessionRepository, $page, $pageSize, $consultationSessionFilter);
    }

}
