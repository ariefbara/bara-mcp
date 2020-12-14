<?php

namespace Query\Application\Service\User;

use Query\Domain\Model\User\UserParticipant;

class ViewProgramParticipation
{

    /**
     *
     * @var ProgramParticipationRepository
     */
    protected $programParticipationRepository;

    public function __construct(ProgramParticipationRepository $programParticipationRepository)
    {
        $this->programParticipationRepository = $programParticipationRepository;
    }

    /**
     * 
     * @param string $userId
     * @param int $page
     * @param int $pageSize
     * @return UserParticipant[]
     */
    public function showAll(string $userId, int $page, int $pageSize, ?bool $activeStatus)
    {
        return $this->programParticipationRepository->all($userId, $page, $pageSize, $activeStatus);
    }

    public function showById(string $userId, string $userParticipantId): UserParticipant
    {
        return $this->programParticipationRepository->ofId($userId, $userParticipantId);
    }

}
