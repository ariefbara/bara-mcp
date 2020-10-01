<?php

namespace Query\Domain\Service\Firm\Program;

use Query\Domain\Model\Firm\{
    Program,
    Program\Participant
};

class ParticipantFinder
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

    public function findParticipantInProgram(Program $program, string $participantId): Participant
    {
        $programId = $program->getId();
        return $this->participantRepository->aParticipantOfProgram($programId, $participantId);
    }

    public function findAllParticipantInProgram(Program $program, int $page, int $pageSize, ?bool $activeStatus)
    {
        $programId = $program->getId();
        return $this->participantRepository->allParticipantsOfProgram($programId, $page, $pageSize, $activeStatus);
    }

}
