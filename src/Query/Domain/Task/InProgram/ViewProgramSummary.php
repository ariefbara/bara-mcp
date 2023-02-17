<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByCoordinator;
use Query\Domain\Task\Dependency\Firm\ProgramRepository;
use Query\Domain\Task\GenericQueryPayload;

class ViewProgramSummary implements ProgramTaskExecutableByCoordinator
{

    /**
     * 
     * @var ProgramRepository
     */
    protected $programRepository;

    public function __construct(ProgramRepository $programRepository)
    {
        $this->programRepository = $programRepository;
    }

    /**
     * 
     * @param string $programId
     * @param GenericQueryPayload $payload
     * @return void
     */
    public function execute(string $programId, $payload): void
    {
        $payload->result = $this->programRepository->aProgramSummary($programId);
    }

}
