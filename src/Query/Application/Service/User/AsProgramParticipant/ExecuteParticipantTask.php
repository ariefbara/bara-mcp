<?php

namespace Query\Application\Service\User\AsProgramParticipant;

use Query\Domain\Model\Firm\Program\ITaskExecutableByParticipant;

class ExecuteParticipantTask
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
    
    public function execute(string $userId, string $participantId, ITaskExecutableByParticipant $task): void
    {
        $this->userParticipantRepository->aUserParticipant($userId, $participantId)
                ->executeTask($task);
    }

}
