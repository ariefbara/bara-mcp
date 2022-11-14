<?php

namespace Participant\Application\Service\Client\ClientParticipant;

use Participant\Application\Service\Client\ClientParticipantRepository;
use Participant\Domain\Task\Participant\ParticipantTask;

class ExecuteTask
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
            string $firmId, string $clientId, string $clientParticipantId, ParticipantTask $task, $payload): void
    {
        $this->clientParticipantRepository->aClientParticipant($firmId, $clientId, $clientParticipantId)
                ->executeTask($task, $payload);
        $this->clientParticipantRepository->update();
    }

}
