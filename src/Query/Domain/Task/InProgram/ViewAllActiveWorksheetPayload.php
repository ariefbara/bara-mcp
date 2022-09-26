<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Task\Dependency\Firm\Program\Participant\WorksheetFilter;

class ViewAllActiveWorksheetPayload
{

    /**
     * 
     * @var WorksheetFilter
     */
    protected $worksheetFilter;
    public $result;

    public function getWorksheetFilter(): WorksheetFilter
    {
        return $this->worksheetFilter;
    }

    public function __construct(WorksheetFilter $worksheetFilter)
    {
        $this->worksheetFilter = $worksheetFilter;
    }

}
