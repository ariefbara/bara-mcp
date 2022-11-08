<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Consultant;

use Query\Domain\Task\Dependency\NoteFilter;
use Resources\PaginationFilter;

class ConsultantNoteFilter
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
    protected $consultantId;

    public function setConsultantId(?string $consultantId)
    {
        $this->consultantId = $consultantId;
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

    public function getConsultantId(): ?string
    {
        return $this->consultantId;
    }

}
