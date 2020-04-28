<?php

namespace Client\Application\Service\Client;

class ProgramParticipationQuit
{
    protected $programParticipationRepository;
    
    function __construct(ProgramParticipationRepository $programParticipationRepository)
    {
        $this->programParticipationRepository = $programParticipationRepository;
    }
    
    public function execute(string $clientId, string $programParticipationId): void
    {
        $this->programParticipationRepository->ofId($clientId, $programParticipationId)
            ->quit();
        $this->programParticipationRepository->update();
    }

}
