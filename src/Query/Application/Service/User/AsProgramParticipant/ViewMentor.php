<?php

namespace Query\Application\Service\User\AsProgramParticipant;

use Query\Domain\Model\Firm\Program\Consultant;
use Query\Domain\Service\Firm\Program\MentorRepository;

class ViewMentor
{

    /**
     * 
     * @var UserParticipantRepository
     */
    protected $userParticipantRepository;

    /**
     * 
     * @var MentorRepository
     */
    protected $mentorRepository;

    public function __construct(UserParticipantRepository $userParticipantRepository, MentorRepository $mentorRepository)
    {
        $this->userParticipantRepository = $userParticipantRepository;
        $this->mentorRepository = $mentorRepository;
    }

    public function showAll(string $userId, string $programId, int $page, int $pageSize)
    {
        return $this->userParticipantRepository->aProgramParticipationOfUserCorrespondWithProgram($userId, $programId)
                        ->viewAllMentors($this->mentorRepository, $page, $pageSize);
    }

    public function showById(string $userId, string $programId, string $mentorId): Consultant
    {
        return $this->userParticipantRepository->aProgramParticipationOfUserCorrespondWithProgram($userId, $programId)
                        ->viewMentor($this->mentorRepository, $mentorId);
    }

}
