<?php

namespace Query\Domain\Task\Personnel\OnDedicatedMentee;

use Query\Domain\Model\Firm\Program\Participant\Worksheet;

class ViewAllWorksheetsBelongsToMenteePayload
{

    /**
     * 
     * @var int
     */
    protected $page;

    /**
     * 
     * @var int
     */
    protected $pageSize;

    /**
     * 
     * @var Worksheet[] | null
     */
    public $result;

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function __construct(int $page, int $pageSize)
    {
        $this->page = $page;
        $this->pageSize = $pageSize;
    }

}
