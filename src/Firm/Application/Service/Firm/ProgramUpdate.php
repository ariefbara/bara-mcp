<?php

namespace Firm\Application\Service\Firm;

use Firm\Domain\Model\Firm\ {
    Program,
    ProgramData
};

class ProgramUpdate
{
    /**
     *
     * @var ProgramRepository
     */
    protected $programRepository;
    
    function __construct(ProgramRepository $programRepository)
    {
        $this->programRepository = $programRepository;
    }
    
    public function execute(string $firmId, string $programId, ProgramData $programData): Program
    {
        $program = $this->programRepository->ofId($firmId, $programId);
        $program->update($programData);
        $this->programRepository->update();
        return $program;
    }

}
