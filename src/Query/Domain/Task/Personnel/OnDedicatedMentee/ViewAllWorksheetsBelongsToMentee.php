<?php

namespace Query\Domain\Task\Personnel\OnDedicatedMentee;

use Query\Domain\Model\Firm\Program\Participant\QueryTaskOnDedicatedMenteeExecutableByDedicatedMentor;
use Query\Domain\Task\Dependency\Firm\Program\Participant\WorksheetRepository;

class ViewAllWorksheetsBelongsToMentee implements QueryTaskOnDedicatedMenteeExecutableByDedicatedMentor
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
     * @param string $menteeId
     * @param ViewAllWorksheetsBelongsToMenteePayload $payload
     * @return void
     */
    public function execute(string $menteeId, $payload): void
    {
        $payload->result = $this->worksheetRepository
                ->allActiveWorksheetsBelongsToParticipant($menteeId, $payload->getPage(), $payload->getPageSize());
    }

}
