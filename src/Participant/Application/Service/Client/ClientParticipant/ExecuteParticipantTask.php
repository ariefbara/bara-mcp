<?php

namespace Participant\Application\Service\Client\ClientParticipant;

use Participant\Application\Service\Client\ClientParticipantRepository;
use Participant\Domain\Model\ITaskExecutableByParticipant;

class ExecuteParticipantTask
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
            string $firmId, string $clientId, string $clientParticipantId, ITaskExecutableByParticipant $task): void
    {
        $this->clientParticipantRepository->aClientParticipant($firmId, $clientId, $clientParticipantId)
                ->executeParticipantTask($task);
        $this->clientParticipantRepository->update();
    }

}
