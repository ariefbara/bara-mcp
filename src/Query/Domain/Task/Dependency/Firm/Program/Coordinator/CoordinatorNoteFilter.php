<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Coordinator;

use Query\Domain\Task\Dependency\NoteFilter;
use Resources\PaginationFilter;

class CoordinatorNoteFilter
{

    /**
     * 
     * @var NoteFilter
     */
    protected $noteFilter;

    /**
     * 
     * @var PaginationFilter
     */
    protected $paginationFilter;

    /**
     * 
     * @var string|null
     */
    protected $coordinatorId;

    public function setCoordinatorId(?string $coordinatorId)
    {
        $this->coordinatorId = $coordinatorId;
        return $this;
    }

    public function __construct(NoteFilter $noteFilter, PaginationFilter $paginationFilter)
    {
        $this->noteFilter = $noteFilter;
        $this->paginationFilter = $paginationFilter;
    }

    public function getNoteFilter(): NoteFilter
    {
        return $this->noteFilter;
    }

    public function getPaginationFilter(): PaginationFilter
    {
        return $this->paginationFilter;
    }

    public function getCoordinatorId(): ?string
    {
        return $this->coordinatorId;
    }

}
