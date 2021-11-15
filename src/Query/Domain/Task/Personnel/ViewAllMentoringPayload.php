<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Task\Dependency\MentoringFilter;

class ViewAllMentoringPayload
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
     * @var MentoringFilter|null
     */
    protected $mentoringFilter;

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function getPageSize(): ?int
    {
        return $this->pageSize;
    }

    public function getMentoringFilter(): ?MentoringFilter
    {
        return $this->mentoringFilter;
    }

    public function setPage(?int $page)
    {
        $this->page = $page;
        return $this;
    }

    public function setPageSize(?int $pageSize)
    {
        $this->pageSize = $pageSize;
        return $this;
    }

    public function setMentoringFilter(?MentoringFilter $mentoringFilter)
    {
        $this->mentoringFilter = $mentoringFilter;
        return $this;
    }

    public function __construct(?int $page, ?int $pageSize, ?MentoringFilter $mentoringFilter)
    {
        $this->page = $page;
        $this->pageSize = $pageSize;
        $this->mentoringFilter = $mentoringFilter;
    }

}
