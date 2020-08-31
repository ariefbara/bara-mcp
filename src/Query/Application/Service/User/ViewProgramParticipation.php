<?php

namespace Query\Application\Service\User;

use Query\Domain\Model\Firm\Program\UserParticipant;

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
    public function showAll(string $userId, int $page, int $pageSize)
    {
        return $this->programParticipationRepository->allProgramParticipationsOfUser($userId, $page, $pageSize);
    }

    public function showById(string $userId, string $firmId, string $programId): UserParticipant
    {
        return $this->programParticipationRepository->aProgramParticipationOfUser($userId, $firmId, $programId);
    }

}
