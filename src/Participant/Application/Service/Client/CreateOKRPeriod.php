<?php

namespace Participant\Application\Service\Client;

use Participant\Application\Service\Participant\OKRPeriodRepository;
use Participant\Domain\Model\Participant\OKRPeriodData;

class CreateOKRPeriod
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
    
    public function execute(string $firmId, string $clientId, string $participantId, OKRPeriodData $okrPeriodData): string
    {
        $id = $this->okrPeriodRepository->nextIdentity();
        $okrPeriod = $this->clientParticipantRepository->aClientParticipant($firmId, $clientId, $participantId)
                ->createOKRPeriod($id, $okrPeriodData);
        $this->okrPeriodRepository->add($okrPeriod);
        return $id;
    }

    
}
