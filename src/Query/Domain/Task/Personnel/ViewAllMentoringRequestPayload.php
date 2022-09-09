<?php

namespace Query\Domain\Task\Personnel;

use DateTimeImmutable;
use Query\Domain\Task\Dependency\Firm\Program\Participant\MentoringRequestSearch;
use Query\Domain\Task\Dependency\MentoringFilter\MentoringRequestFilter;

class ViewAllMentoringRequestPayload
{

    /**
     * 
     * @var int|null
     */
    protected $page;

    /**
     * 
     * @var int|null
     */
    protected $pageSize;

    /**
     * 
     * @var MentoringRequestSearch
     */
    protected $mentoringRequestSearch;
    public $result;

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function getPageSize(): ?int
    {
        return $this->pageSize;
    }

    public function getMentoringRequestSearch(): MentoringRequestSearch
    {
        return $this->mentoringRequestSearch;
    }

    public function __construct(?int $page, ?int $pageSize, MentoringRequestSearch $mentoringRequestSearch)
    {
        $this->page = $page;
        $this->pageSize = $pageSize;
        $this->mentoringRequestSearch = $mentoringRequestSearch;
    }

}
