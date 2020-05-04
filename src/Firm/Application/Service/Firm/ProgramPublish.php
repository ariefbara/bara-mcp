<?php

namespace Firm\Application\Service\Firm;

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

    public function execute(string $firmId, string $programId): void
    {
        $program = $this->programRepository->ofId($firmId, $programId);
        $program->publish();
        $this->programRepository->update();
    }
}
