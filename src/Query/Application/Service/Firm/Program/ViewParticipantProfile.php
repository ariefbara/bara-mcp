<?php

namespace Query\Application\Service\Firm\Program;

use Query\Domain\Model\Firm\Program\Participant\ParticipantProfile;

class ViewParticipantProfile
{

    /**
     * 
     * @var ParticipantProfileRepository
     */
    protected $participantProfileRepository;

    function __construct(ParticipantProfileRepository $participantProfileRepository)
    {
        $this->participantProfileRepository = $participantProfileRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $programId
     * @param string $participantId
     * @param int $page
     * @param int $pageSize
     * @return ParticipantProfile[]
     */
    public function showAll(string $firmId, string $programId, string $participantId, int $page, int $pageSize)
    {
        return $this->participantProfileRepository->allProfilesBelongsToParticipantInProgram(
                        $firmId, $programId, $participantId, $page, $pageSize);
    }

    public function showById(string $firmId, string $programId, string $participantProfileId): ParticipantProfile
    {
        return $this->participantProfileRepository
                        ->aParticipantProfileInProgram($firmId, $programId, $participantProfileId);
    }

}
