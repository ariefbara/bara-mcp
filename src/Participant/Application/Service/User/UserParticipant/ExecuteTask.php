<?php

namespace Participant\Application\Service\User\UserParticipant;

use Participant\Application\Service\User\UserParticipantRepository;
use Participant\Domain\Task\Participant\ParticipantTask;

class ExecuteTask
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
    
    public function execute(string $userId, string $userParticipantId, ParticipantTask $task, $payload): void
    {
        $this->userParticipantRepository->aUserParticipant($userId, $userParticipantId)
                ->executeTask($task, $payload);
        $this->userParticipantRepository->update();
    }

}
