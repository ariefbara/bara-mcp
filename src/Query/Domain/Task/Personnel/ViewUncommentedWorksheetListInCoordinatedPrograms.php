<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByCoordinator;
use Query\Domain\Task\Dependency\Firm\Program\Participant\WorksheetRepository;

class ViewUncommentedWorksheetListInCoordinatedPrograms implements PersonnelTask
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
     * @param string $personnelId
     * @param ViewUncommentedWorksheetListInCoordinatedProgramsPayload $payload
     * @return void
     */
    public function execute(string $personnelId, $payload): void
    {
        $payload->result = $this->worksheetRepository
                ->uncommentedWorksheetListInProgramsCoordinatedByPersonnel($personnelId, $payload->getPaginationFilter());
    }

}
