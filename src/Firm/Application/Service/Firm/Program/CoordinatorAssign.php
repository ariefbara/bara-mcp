<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Application\Service\Firm\ {
    PersonnelRepository,
    ProgramRepository
};

class CoordinatorAssign
{

    protected $programRepository;
    protected $personnelRepository;

    function __construct(ProgramRepository $programRepository, PersonnelRepository $personnelRepository)
    {
        $this->programRepository = $programRepository;
        $this->personnelRepository = $personnelRepository;
    }
    
    public function execute(string $firmId, string $programId, string $personnelId): string
    {
        $personnel = $this->personnelRepository->ofId($firmId, $personnelId);
        $coordinatorId = $this->programRepository->ofId($firmId, $programId)
                ->assignPersonnelAsCoordinator($personnel);
        $this->programRepository->update();
        return $coordinatorId;
    }

}
