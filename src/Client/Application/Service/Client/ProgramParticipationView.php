<?php

namespace Client\Application\Service\Client;

use Client\Domain\Model\Client\ProgramParticipation;

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

    public function showById(string $clientId, string $programParticipationId): ProgramParticipation
    {
        return $this->programParticipationRepository->ofId($clientId, $programParticipationId);
    }

    /**
     * 
     * @param string $clientId
     * @param int $page
     * @param int $pageSize
     * @return ProgramParticipation[]
     */
    public function showAll(string $clientId, int $page, int $pageSize)
    {
        return $this->programParticipationRepository->all($clientId, $page, $pageSize);
    }

}
