<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Task\Dependency\Firm\Program\Consultant\ConsultantNoteFilter;

class ViewAllOwnedConsultantNotePayload
{

    /**
     * 
     * @var ConsultantNoteFilter
     */
    protected $consultantNoteFilter;
    public $result;

    public function getConsultantNoteFilter(): ConsultantNoteFilter
    {
        return $this->consultantNoteFilter;
    }

    public function __construct(ConsultantNoteFilter $consultantNoteFilter)
    {
        $this->consultantNoteFilter = $consultantNoteFilter;
    }

}
