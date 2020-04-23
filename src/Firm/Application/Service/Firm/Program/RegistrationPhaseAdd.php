<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\ {
    Application\Service\Firm\ProgramRepository,
    Domain\Model\Firm\Program\RegistrationPhase,
    Domain\Model\Firm\Program\RegistrationPhaseData
};

class RegistrationPhaseAdd
{

    protected $registrationPhaseRepository;
    protected $programRepository;

    function __construct(RegistrationPhaseRepository $registrationPhaseRepository, ProgramRepository $programRepository)
    {
        $this->registrationPhaseRepository = $registrationPhaseRepository;
        $this->programRepository = $programRepository;
    }
    
    public function execute(string $firmId, string $programId, RegistrationPhaseData $registrationPhaseData): RegistrationPhase
    {
        $program = $this->programRepository->ofId($firmId, $programId);
        $id = $this->registrationPhaseRepository->nextIdentity();
        
        $registrationPhase = new RegistrationPhase($program, $id, $registrationPhaseData);
        $this->registrationPhaseRepository->add($registrationPhase);
        return $registrationPhase;
    }

}
