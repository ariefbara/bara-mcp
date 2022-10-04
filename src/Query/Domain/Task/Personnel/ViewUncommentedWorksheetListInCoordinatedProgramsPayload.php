<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Task\Dependency\PaginationFilter;

class ViewUncommentedWorksheetListInCoordinatedProgramsPayload
{

    /**
     * 
     * @var PaginationFilter
     */
    protected $paginationFilter;
    public $result;

    public function getPaginationFilter(): PaginationFilter
    {
        return $this->paginationFilter;
    }

    public function __construct(PaginationFilter $paginationFilter)
    {
        $this->paginationFilter = $paginationFilter;
    }

}
