<?php

namespace Participant\Application\Service\Client;

use Participant\Application\Service\Participant\OKRPeriodRepository;
use Participant\Domain\Model\Participant\OKRPeriodData;

class UpdateOKRPeriod
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
    
    public function execute(
            string $firmId, string $clientId, string $participantId, string $okrPeriodId, OKRPeriodData $okrPeriodData): void
    {
        $okrPeriod = $this->okrPeriodRepository->ofId($okrPeriodId);
        $this->clientParticipantRepository->aClientParticipant($firmId, $clientId, $participantId)
                ->updateOKRPeriod($okrPeriod, $okrPeriodData);
        $this->clientParticipantRepository->update();
    }

}
