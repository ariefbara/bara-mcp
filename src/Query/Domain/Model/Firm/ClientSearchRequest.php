<?php

namespace Query\Domain\Model\Firm;

class ClientSearchRequest
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
     * @var array
     */
    protected $integerFieldSearchRequest;

    /**
     * 
     * @var array
     */
    protected $stringFieldSearchRequest;

    /**
     * 
     * @var array
     */
    protected $textAreaFieldSearchRequest;

    /**
     * 
     * @var array
     */
    protected $singleSelectFieldSearchRequest;

    /**
     * 
     * @var array
     */
    protected $multiSelectFieldSearchRequest;
    
    public function getPage(): int
    {
        return $this->page;
    }
    
    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function getIntegerFieldSearchRequest(): array
    {
        return $this->integerFieldSearchRequest;
    }

    public function getStringFieldSearchRequest(): array
    {
        return $this->stringFieldSearchRequest;
    }

    public function getTextAreaFieldSearchRequest(): array
    {
        return $this->textAreaFieldSearchRequest;
    }

    public function getSingleSelectFieldSearchRequest(): array
    {
        return $this->singleSelectFieldSearchRequest;
    }

    public function getMultiSelectFieldSearchRequest(): array
    {
        return $this->multiSelectFieldSearchRequest;
    }
    
    public function __construct(int $page, int $pageSize)
    {
        $this->page = $page;
        $this->pageSize = $pageSize;
        $this->integerFieldSearchRequest = [];
        $this->stringFieldSearchRequest = [];
        $this->textAreaFieldSearchRequest = [];
        $this->singleSelectFieldSearchRequest = [];
        $this->multiSelectFieldSearchRequest = [];
    }

    public function addIntegerFieldSearch(string $fieldId, int $value): void
    {
        $this->integerFieldSearchRequest[$fieldId] = $value;
    }

    public function addStringFieldSearch(string $fieldId, string $value): void
    {
        $this->stringFieldSearchRequest[$fieldId] = $value;
    }

    public function addTextAreaFieldSearch(string $fieldId, string $value): void
    {
        $this->textAreaFieldSearchRequest[$fieldId] = $value;
    }

    public function addSingleSelectFieldSearch(string $fieldId, array $listOfOptionId): void
    {
        $this->singleSelectFieldSearchRequest[$fieldId] = $listOfOptionId;
    }

    public function addMultiSelectFieldSearch(string $fieldId, array $listOfOptionId): void
    {
        $this->multiSelectFieldSearchRequest[$fieldId] = $listOfOptionId;
    }
    
    public function getOffset(): int
    {
        return $this->pageSize * ($this->page - 1);
    }
    
}
