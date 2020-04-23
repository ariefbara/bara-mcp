<?php

namespace Firm\Application\Service\Firm;

use Firm\Domain\Model\Firm\Program;

class ProgramPublish
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

    public function execute(string $firmId, string $programId): Program
    {
        $program = $this->programRepository->ofId($firmId, $programId);
        $program->publish();
        $this->programRepository->update();
        return $program;
    }
}
