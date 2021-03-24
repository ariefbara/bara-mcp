<?php

namespace Query\Application\Service\User\AsProgramParticipant;

use Query\Domain\Model\Firm\Program\DedicatedMentorRepository;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor;

class ViewDedicatedMentor
{

    /**
     * 
     * @var UserParticipantRepository
     */
    protected $userParticipantRepository;

    /**
     * 
     * @var DedicatedMentorRepository
     */
    protected $dedicatedMentorRepository;

    public function __construct(
            UserParticipantRepository $userParticipantRepository, DedicatedMentorRepository $dedicatedMentorRepository)
    {
        $this->userParticipantRepository = $userParticipantRepository;
        $this->dedicatedMentorRepository = $dedicatedMentorRepository;
    }

    /**
     * 
     * @param string $userId
     * @param string $participantId
     * @param int $page
     * @param int $pageSize
     * @param bool|null $cancelledStatus
     * @return DedicatedMentor[]
     */
    public function showAll(string $userId, string $participantId, int $page, int $pageSize, ?bool $cancelledStatus)
    {
        return $this->userParticipantRepository->aUserParticipant($userId, $participantId)
                        ->viewAllDedicatedMentors($this->dedicatedMentorRepository, $page, $pageSize, $cancelledStatus);
    }

    public function showById(string $userId, string $participantId, string $dedicatedMentorId): DedicatedMentor
    {
        return $this->userParticipantRepository->aUserParticipant($userId, $participantId)
                        ->viewDedicatedMentor($this->dedicatedMentorRepository, $dedicatedMentorId);
    }

}
