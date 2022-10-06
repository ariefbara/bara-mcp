<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Task\Dependency\Firm\ProgramRepository;
use Query\Domain\Task\GenericQueryPayload;

class ViewCoordinatedProgramsSummary implements PersonnelTask
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
     * @param string $personnelId
     * @param GenericQueryPayload $payload
     * @return void
     */
    public function execute(string $personnelId, $payload): void
    {
        $payload->result = $this->programRepository->allProgramsSummaryCoordinatedByPersonnel($personnelId);
    }

}
