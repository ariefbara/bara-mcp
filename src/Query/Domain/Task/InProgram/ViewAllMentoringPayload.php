<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Task\Dependency\ExtendedMentoringFilter;

class ViewAllMentoringPayload
{

    /**
     * 
     * @var ExtendedMentoringFilter
     */
    protected $filter;
    public $result;

    public function getFilter(): ExtendedMentoringFilter
    {
        return $this->filter;
    }

    public function __construct(ExtendedMentoringFilter $filter)
    {
        $this->filter = $filter;
    }

}
