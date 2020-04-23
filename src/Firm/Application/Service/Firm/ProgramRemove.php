<?php

namespace Firm\Application\Service\Firm;

class ProgramRemove
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
        $this->programRepository->ofId($firmId, $programId)->remove();
        $this->programRepository->update();
    }
}
