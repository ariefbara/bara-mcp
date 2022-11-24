<?php

namespace Query\Application\Service\Client\AsProgramParticipant;

use Query\Domain\Task\Participant\ParticipantQueryTask;

class ExecuteParticipantQueryTask
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

    public function execute(
            string $firmId, string $clientId, string $participantId, ParticipantQueryTask $task, $payload): void
    {
        $this->clientParticipantRepository->aClientParticipant($firmId, $clientId, $participantId)
                ->executeQueryTask($task, $payload);
    }

}
