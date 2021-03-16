<?php

namespace Participant\Application\Service\Client;

use Participant\Application\Service\Participant\OKRPeriodRepository;

class CancelOKRPeriod
{
    /**
     * 
     * @var ClientParticipantRepository
     */
    protected $clientParticipantRepository;

    /**
     * 
     * @var OKRPeriodRepository
     */
    protected $okrPeriodRepository;
    
    public function __construct(ClientParticipantRepository $clientParticipantRepository,
            OKRPeriodRepository $okrPeriodRepository)
    {
        $this->clientParticipantRepository = $clientParticipantRepository;
        $this->okrPeriodRepository = $okrPeriodRepository;
    }
    
    public function execute(string $firmId, string $clientId, string $participantId, string $okrPeriodId): void
    {
        $okrPeriod = $this->okrPeriodRepository->ofId($okrPeriodId);
        $this->clientParticipantRepository->aClientParticipant($firmId, $clientId, $participantId)
                ->cancelOKRPeriod($okrPeriod);
        $this->clientParticipantRepository->update();
    }
}
