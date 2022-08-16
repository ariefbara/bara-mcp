<?php

namespace Query\Application\Service\Firm\Program;

use Query\Domain\Model\Firm\Program\Participant;

class ViewParticipant
{

    /**
     *
     * @var ParticipantRepository
     */
    protected $participantRepository;

    public function __construct(ParticipantRepository $participantRepository)
    {
        $this->participantRepository = $participantRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $programId
     * @param int $page
     * @param int $pageSize
     * @return Participant[]
     */
    public function showAll(
            string $firmId, string $programId, int $page, int $pageSize, ?array $status, ?string $searchByName)
    {
        return $this->participantRepository
                        ->all($firmId, $programId, $page, $pageSize, $status, $searchByName);
    }

    public function showById(string $firmId, string $programId, string $participantId): Participant
    {
        return $this->participantRepository->ofId($firmId, $programId, $participantId);
    }

}
