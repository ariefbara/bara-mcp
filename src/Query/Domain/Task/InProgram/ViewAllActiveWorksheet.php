<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByCoordinator;
use Query\Domain\Task\Dependency\Firm\Program\Participant\WorksheetRepository;

class ViewAllActiveWorksheet implements ProgramTaskExecutableByCoordinator
{

    /**
     * 
     * @var WorksheetRepository
     */
    protected $worksheetRepository;

    public function __construct(WorksheetRepository $worksheetRepository)
    {
        $this->worksheetRepository = $worksheetRepository;
    }

    /**
     * 
     * @param string $programId
     * @param ViewAllActiveWorksheetPayload $payload
     * @return void
     */
    public function execute(string $programId, $payload): void
    {
        $payload->result = $this->worksheetRepository
                ->allActiveWorksheetsInProgram($programId, $payload->getWorksheetFilter());
    }

}
