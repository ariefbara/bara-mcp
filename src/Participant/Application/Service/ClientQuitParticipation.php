<?php

namespace Participant\Application\Service;

class ClientQuitParticipation
{

    /**
     *
     * @var ClientParticipantRepository
     */
    protected $clientParticipantRepository;

    public function __construct(ClientParticipantRepository $clientParticipantRepository)
    {
        $this->clientParticipantRepository = $clientParticipantRepository;
    }
    
    public function execute(string $firmId, string $clientId, string $programParticipationId): void
    {
        $this->clientParticipantRepository->ofId($firmId, $clientId, $programParticipationId)->quit();
        $this->clientParticipantRepository->update();
    }

}
