<?php

namespace Firm\Application\Service\Firm;

use Firm\{
    Application\Service\FirmRepository,
    Domain\Model\Firm\Program,
    Domain\Model\Firm\ProgramData
};

class ProgramAdd
{

    protected $programRepository;
    protected $firmRepository;

    function __construct(ProgramRepository $programRepository, FirmRepository $firmRepository)
    {
        $this->programRepository = $programRepository;
        $this->firmRepository = $firmRepository;
    }

    public function execute(string $firmId, ProgramData $programData): Program
    {
        $firm = $this->firmRepository->ofId($firmId);
        $id = $this->programRepository->nextIdentity();
        $program = new Program($firm, $id, $programData);
        $this->programRepository->add($program);
        return $program;
    }

}
