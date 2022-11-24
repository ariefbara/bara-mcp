<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Task\CommonViewListPayload;
use Query\Domain\Task\Dependency\Firm\Program\Participant\WorksheetRepository;

class ViewWorksheetListInCoordinatedProgram implements PersonnelTask
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
     * @param CommonViewListPayload $payload
     * @return void
     */
    public function execute(string $personnelId, $payload): void
    {
        $payload->result = $this->worksheetRepository
                ->worksheetListInAllProgramsCoordinatedByParticipant($personnelId, $payload->getFilter());
    }

}
