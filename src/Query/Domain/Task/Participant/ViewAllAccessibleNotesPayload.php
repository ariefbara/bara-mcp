<?php

namespace Query\Domain\Task\Participant;

use Query\Domain\Task\Dependency\NoteFilter;

class ViewAllAccessibleNotesPayload
{

    /**
     * 
     * @var NoteFilter
     */
    protected $noteFilter;
    public $result;

    public function __construct(NoteFilter $noteFilter)
    {
        $this->noteFilter = $noteFilter;
    }

    public function getNoteFilter(): NoteFilter
    {
        return $this->noteFilter;
    }

}
