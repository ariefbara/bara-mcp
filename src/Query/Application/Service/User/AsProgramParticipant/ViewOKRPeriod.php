<?php

namespace Query\Application\Service\User\AsProgramParticipant;

use Query\Application\Service\TeamMember\OKRPeriodRepository;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod;

class ViewOKRPeriod
{

    /**
     * 
     * @var UserParticipantRepository
     */
    protected $userParticipantRepository;

    /**
     * 
     * @var OKRPeriodRepository
     */
    protected $okrPeriodRepository;

    public function __construct(UserParticipantRepository $userParticipantRepository,
            OKRPeriodRepository $okrPeriodRepository)
    {
        $this->userParticipantRepository = $userParticipantRepository;
        $this->okrPeriodRepository = $okrPeriodRepository;
    }
    
    /**
     * 
     * @param string $firmId
     * @param string $userId
     * @param string $participantId
     * @param int $page
     * @param int $pageSize
     * @return OKRPeriod[]
     */
    public function showAll(string $userId, string $participantId, int $page, int $pageSize)
    {
        return $this->userParticipantRepository->aUserParticipant($userId, $participantId)
                ->viewAllOKRPeriod($this->okrPeriodRepository, $page, $pageSize);
    }
    
    public function showById(string $userId, string $participantId, string $okrPeriodId): OKRPeriod
    {
        return $this->userParticipantRepository->aUserParticipant($userId, $participantId)
                ->viewOKRPeriod($this->okrPeriodRepository, $okrPeriodId);
    }

}
