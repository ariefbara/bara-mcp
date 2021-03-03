<?php

namespace Participant\Application\Service\User;

use Participant\Application\Service\Participant\OKRPeriodRepository;

class CancelOKRPeriod
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
    
    public function execute(string $userId, string $participantId, string $okrPeriodId): void
    {
        $okrPeriod = $this->okrPeriodRepository->ofId($okrPeriodId);
        $this->userParticipantRepository->aUserParticipant($userId, $participantId)
                ->cancelOKRPeriod($okrPeriod);
        $this->userParticipantRepository->update();
    }
}
