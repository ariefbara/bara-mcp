<?php

namespace Query\Domain\Task\Dependency\Firm;

class ClientFilter
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
     * @var string|null
     */
    protected $name;

    /**
     * 
     * @var string|null
     */
    protected $email;

    /**
     * 
     * @var bool|null
     */
    protected $activatedStatus;

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function getPageSize(): ?int
    {
        return $this->pageSize;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getActivatedStatus(): ?bool
    {
        return $this->activatedStatus;
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

    public function setName(?string $name)
    {
        $this->name = $name;
        return $this;
    }

    public function setEmail(?string $email)
    {
        $this->email = $email;
        return $this;
    }

    public function setActivatedStatus(?bool $activatedStatus)
    {
        $this->activatedStatus = $activatedStatus;
        return $this;
    }

    public function __construct()
    {
        
    }

}
