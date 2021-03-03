<?php

namespace Participant\Application\Service\User;

use Participant\Application\Service\Participant\OKRPeriodRepository;
use Participant\Domain\Model\Participant\OKRPeriodData;

class CreateOKRPeriod
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
    
    public function execute(string $userId, string $participantId, OKRPeriodData $okrPeriodData): string
    {
        $id = $this->okrPeriodRepository->nextIdentity();
        $okrPeriod = $this->userParticipantRepository->aUserParticipant($userId, $participantId)
                ->createOKRPeriod($id, $okrPeriodData);
        $this->okrPeriodRepository->add($okrPeriod);
        return $id;
    }

    
}
