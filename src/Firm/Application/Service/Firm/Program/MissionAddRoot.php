<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\{
    Application\Service\Firm\ProgramRepository,
    Application\Service\Firm\WorksheetFormRepository,
    Domain\Model\Firm\Program\Mission
};

class MissionAddRoot
{

    protected $missionRepository;
    protected $programRepository;
    protected $worksheetFormRepository;

    function __construct(MissionRepository $missionRepository, ProgramRepository $programRepository,
            WorksheetFormRepository $worksheetFormRepository)
    {
        $this->missionRepository = $missionRepository;
        $this->programRepository = $programRepository;
        $this->worksheetFormRepository = $worksheetFormRepository;
    }

    public function execute(
            string $firmId, string $programId, string $name, ?string $description, string $worksheetFormId,
            ?string $position): string
    {
        $program = $this->programRepository->ofId($firmId, $programId);
        $id = $this->missionRepository->nextIdentity();
        $worksheetForm = $this->worksheetFormRepository->ofId($firmId, $worksheetFormId);

        $mission = Mission::createRoot($program, $id, $name, $description, $worksheetForm, $position);
        $this->missionRepository->add($mission);
        return $id;
    }

}
