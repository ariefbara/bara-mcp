<?php

namespace Query\Application\Service\Client\AsProgramParticipant;

use Query\Application\Service\Participant\OKRPeriodRepository;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod;

class ViewOKRPeriod
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
    
    /**
     * 
     * @param string $firmId
     * @param string $clientId
     * @param string $participantId
     * @param int $page
     * @param int $pageSize
     * @return OKRPeriod[]
     */
    public function showAll(string $firmId, string $clientId, string $participantId, int $page, int $pageSize)
    {
        return $this->clientParticipantRepository->aClientParticipant($firmId, $clientId, $participantId)
                ->viewAllOKRPeriod($this->okrPeriodRepository, $page, $pageSize);
    }
    
    public function showById(string $firmId, string $clientId, string $participantId, string $okrPeriodId): OKRPeriod
    {
        return $this->clientParticipantRepository->aClientParticipant($firmId, $clientId, $participantId)
                ->viewOKRPeriod($this->okrPeriodRepository, $okrPeriodId);
    }

}
