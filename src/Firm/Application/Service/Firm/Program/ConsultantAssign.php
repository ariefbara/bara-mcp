<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Application\Service\Firm\ {
    PersonnelRepository,
    ProgramRepository
};

class ConsultantAssign
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
        $consultantId = $this->programRepository->ofId($firmId, $programId)
                ->assignPersonnelAsConsultant($personnel);
        $this->programRepository->update();
        return $consultantId;
    }

}
