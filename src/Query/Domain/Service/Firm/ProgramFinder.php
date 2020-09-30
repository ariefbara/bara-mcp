<?php

namespace Query\Domain\Service\Firm;

use Query\Domain\Model\Firm\ {
    ParticipantTypes,
    Program,
    Team
};

class ProgramFinder
{

    /**
     *
     * @var ProgramRepository
     */
    protected $programRepository;

    public function __construct(ProgramRepository $programRepository)
    {
        $this->programRepository = $programRepository;
    }

    public function findProgramAvaiableForTeam(Team $team, string $programId): Program
    {
        $firmId = $team->getFirm()->getId();
        return $this->programRepository->ofId($firmId, $programId);
    }

    public function findAllProgramsAvailableForTeam(Team $team, int $page, int $pageSize)
    {
        $firmId = $team->getFirm()->getId();
        return $this->programRepository->all($firmId, $page, $pageSize, ParticipantTypes::TEAM_TYPE);
    }

}
