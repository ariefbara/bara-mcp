<?php

namespace Participant\Application\Service\User;

use Participant\Application\Service\Participant\OKRPeriodRepository;
use Participant\Domain\Model\Participant\OKRPeriodData;

class UpdateOKRPeriod
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
    
    public function execute(string $userId, string $participantId, string $okrPeriodId, OKRPeriodData $okrPeriodData): void
    {
        $okrPeriod = $this->okrPeriodRepository->ofId($okrPeriodId);
        $this->userParticipantRepository->aUserParticipant($userId, $participantId)
                ->updateOKRPeriod($okrPeriod, $okrPeriodData);
        $this->userParticipantRepository->update();
    }

}
