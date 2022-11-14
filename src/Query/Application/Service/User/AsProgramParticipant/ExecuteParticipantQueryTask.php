<?php

namespace Query\Application\Service\User\AsProgramParticipant;

use Query\Domain\Task\Participant\ParticipantQueryTask;

class ExecuteParticipantQueryTask
{

    /**
     * 
     * @var UserParticipantRepository
     */
    protected $userParticipantRepository;

    public function __construct(UserParticipantRepository $userParticipantRepository)
    {
        $this->userParticipantRepository = $userParticipantRepository;
    }
    
    public function execute(string $userId, string $participantId, ParticipantQueryTask $task, $payload): void
    {
        $this->userParticipantRepository->aUserParticipant($userId, $participantId)
                ->executeQueryTask($task, $payload);
    }

}
