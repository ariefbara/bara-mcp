<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\ {
    Application\Service\Firm\PersonnelRepository,
    Application\Service\Firm\ProgramRepository,
    Domain\Model\Firm\Program\Consultant
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

    public function execute(string $firmId, string $programId, string $personnelId): Consultant
    {
        
        $personnel = $this->personnelRepository->ofId($firmId, $personnelId);
        $consultant = $this->programRepository->ofId($firmId, $programId)
                ->assignPersonnelAsConsultant($personnel);
        $this->programRepository->update();
        return $consultant;
    }

}
