<?php

namespace Query\Application\Service\Client\AsProgramParticipant;

use Query\Domain\Model\Firm\Program\ITaskExecutableByParticipant;

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
    
    public function execute(string $firmId, string $clientId, string $participantId, ITaskExecutableByParticipant $task): void
    {
        $this->clientParticipantRepository->aClientParticipant($firmId, $clientId, $participantId)
                ->executeTask($task);
    }

}
