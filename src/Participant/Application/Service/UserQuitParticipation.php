<?php

namespace Participant\Application\Service;

class UserQuitParticipation
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
    
    public function execute(string $userId, string $programParticipationId): void
    {
        $this->userParticipantRepository->ofId($userId, $programParticipationId)->quit();
        $this->userParticipantRepository->update();
    }

}
