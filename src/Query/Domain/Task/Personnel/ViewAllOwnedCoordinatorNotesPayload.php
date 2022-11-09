<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Task\Dependency\Firm\Program\Coordinator\CoordinatorNoteFilter;

class ViewAllOwnedCoordinatorNotesPayload
{

    /**
     * 
     * @var CoordinatorNoteFilter
     */
    protected $coordinatorNoteFilter;
    public $result;

    public function __construct(CoordinatorNoteFilter $coordinatorNoteFilter)
    {
        $this->coordinatorNoteFilter = $coordinatorNoteFilter;
    }

    public function getCoordinatorNoteFilter(): CoordinatorNoteFilter
    {
        return $this->coordinatorNoteFilter;
    }

}
