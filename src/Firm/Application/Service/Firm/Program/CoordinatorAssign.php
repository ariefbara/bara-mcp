<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\ {
    Application\Service\Firm\PersonnelRepository,
    Application\Service\Firm\ProgramRepository,
    Domain\Model\Firm\Program\Coordinator
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
    
    public function execute(string $firmId, string $programId, string $personnelId): Coordinator
    {
        $personnel = $this->personnelRepository->ofId($firmId, $personnelId);
        $coordinator = $this->programRepository->ofId($firmId, $programId)
                ->assignPersonnelAsCoordinator($personnel);
        $this->programRepository->update();
        return $coordinator;
    }

}
