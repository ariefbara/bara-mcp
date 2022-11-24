<?php

namespace Query\Domain\Task;

class CommonViewListPayload
{

    protected $filter;
    public $result;

    public function __construct($filter)
    {
        $this->filter = $filter;
    }

    public function getFilter()
    {
        return $this->filter;
    }

}
