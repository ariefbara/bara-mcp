<?php

namespace Query\Domain\Task\Personnel;

use Resources\OffsetLimit;
use Resources\SearchFilter;

class ViewAllNewProgramApplicantPayload
{

    /**
     * 
     * @var SearchFilter
     */
    protected $searchFilter;

    /**
     * 
     * @var OffsetLimit
     */
    protected $offsetLimit;
    public $result;

    public function getSearchFilter(): SearchFilter
    {
        return $this->searchFilter;
    }

    public function getOffsetLimit(): OffsetLimit
    {
        return $this->offsetLimit;
    }

    public function setSearchFilter(SearchFilter $searchFilter)
    {
        $this->searchFilter = $searchFilter;
        return $this;
    }

    public function setOffsetLimit(OffsetLimit $offsetLimit)
    {
        $this->offsetLimit = $offsetLimit;
        return $this;
    }

}
