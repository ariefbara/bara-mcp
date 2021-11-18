<?php

namespace Query\Domain\Task\Dependency\Firm;

use Query\Domain\Task\Dependency\PaginationFilter;

class TeamFilter
{

    /**
     * 
     * @var PaginationFilter
     */
    protected $paginationFilter;

    /**
     * 
     * @var string|null
     */
    protected $name;

    /**
     * 
     * @var int|null
     */
    protected $minimumActiveMemberCount;

    public function getPage(): int
    {
        return $this->paginationFilter->getPage();
    }

    public function getPageSize(): int
    {
        return $this->paginationFilter->getPageSize();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getMinimumActiveMemberCount(): ?int
    {
        return $this->minimumActiveMemberCount;
    }

    public function setName(?string $name)
    {
        $this->name = $name;
        return $this;
    }

    public function setMinimumActiveMemberCount(?int $minimumActiveMemberCount)
    {
        $this->minimumActiveMemberCount = $minimumActiveMemberCount;
        return $this;
    }

    public function __construct(PaginationFilter $paginationFilter)
    {
        $this->paginationFilter = $paginationFilter;
    }

    public static function create(PaginationFilter $paginationFilter): self
    {
        return new static($paginationFilter);
    }

}
