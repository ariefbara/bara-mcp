<?php

namespace Query\Application\Service\Client\AsProgramParticipant;

use Query\Domain\Model\Firm\Program\ITaskInProgramExecutableByParticipant;

class ExecuteProgramTask
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
            string $firmId, string $clientId, string $participantId, ITaskInProgramExecutableByParticipant $task): void
    {
        $this->clientParticipantRepository->aClientParticipant($firmId, $clientId, $participantId)
                ->executeTaskInProgram($task);
    }

}
