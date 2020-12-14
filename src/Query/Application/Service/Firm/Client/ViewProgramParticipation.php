<?php

namespace Query\Application\Service\Firm\Client;

use Query\Domain\Model\Firm\Client\ClientParticipant;

class ViewProgramParticipation
{

    /**
     *
     * @var ProgramParticipationRepository
     */
    protected $programParticipationRepository;

    public function __construct(ProgramParticipationRepository $programParticipationRepository)
    {
        $this->programParticipationRepository = $programParticipationRepository;
    }
    
    /**
     * 
     * @param string $firmId
     * @param string $clientId
     * @param int $page
     * @param int $pageSize
     * @return UserParticipant[]
     */
    public function showAll(string $firmId, string $clientId, int $page, int $pageSize, ?bool $activeStatus)
    {
        return $this->programParticipationRepository->all($firmId, $clientId, $page, $pageSize, $activeStatus);
    }
    
    public function showById(string $firmId, string $clientId, string $programParticipationId): ClientParticipant
    {
        return $this->programParticipationRepository->ofId($firmId, $clientId, $programParticipationId);
    }
}
