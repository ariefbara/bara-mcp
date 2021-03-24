<?php

namespace Query\Application\Service\Client\AsProgramParticipant;

use Query\Domain\Model\Firm\Program\DedicatedMentorRepository;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor;

class ViewDedicatedMentor
{

    /**
     * 
     * @var ClientParticipantRepository
     */
    protected $clientParticipantRepository;

    /**
     * 
     * @var DedicatedMentorRepository
     */
    protected $dedicatedMentorRepository;

    public function __construct(
            ClientParticipantRepository $clientParticipantRepository,
            DedicatedMentorRepository $dedicatedMentorRepository)
    {
        $this->clientParticipantRepository = $clientParticipantRepository;
        $this->dedicatedMentorRepository = $dedicatedMentorRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $clientId
     * @param string $participantId
     * @param int $page
     * @param int $pageSize
     * @param bool|null $cancelledStatus
     * @return DedicatedMentor[]
     */
    public function showAll(
            string $firmId, string $clientId, string $participantId, int $page, int $pageSize, ?bool $cancelledStatus)
    {
        return $this->clientParticipantRepository->aClientParticipant($firmId, $clientId, $participantId)
                        ->viewAllDedicatedMentors($this->dedicatedMentorRepository, $page, $pageSize, $cancelledStatus);
    }

    public function showById(string $firmId, string $clientId, string $participantId, string $dedicatedMentorId): DedicatedMentor
    {
        return $this->clientParticipantRepository->aClientParticipant($firmId, $clientId, $participantId)
                        ->viewDedicatedMentor($this->dedicatedMentorRepository, $dedicatedMentorId);
    }

}
