<?php

namespace Query\Application\Service\Client;

use Query\Domain\Model\Firm\Program\Participant;

class ProgramParticipationView
{
    /**
     *
     * @var ProgramParticipationRepository
     */
    protected $programParticipationRepository;
    
    function __construct(ProgramParticipationRepository $programParticipationRepository)
    {
        $this->programParticipationRepository = $programParticipationRepository;
    }
    
    /**
     * 
     * @param string $clientId
     * @param int $page
     * @param int $pageSize
     * @return Participant[]
     */
    public function showAll(string $clientId, int $page, int $pageSize)
    {
        return $this->programParticipationRepository->allProgramParticipationsOfClient($clientId, $page, $pageSize);
    }
    
    public function showById(string $clientId, string $programParticipationId): Participant
    {
        return $this->programParticipationRepository->aProgramParticipationOfClient($clientId, $programParticipationId);
    }

}
