<?php

namespace Firm\Domain\Task\BioSearchFilterDataBuilder;

class BioFormSearchFilterRequest
{

    /**
     * 
     * @var string
     */
    protected $bioFormId;

    /**
     * 
     * @var array
     */
    protected $integerFieldSearchFilterRequests;

    /**
     * 
     * @var array
     */
    protected $stringFieldSearchFilterRequests;

    /**
     * 
     * @var array
     */
    protected $textAreaFieldSearchFilterRequests;

    /**
     * 
     * @var array
     */
    protected $singleSelectFieldSearchFilterRequests;

    /**
     * 
     * @var array
     */
    protected $multiSelectFieldSearchFilterRequests;

    public function getBioFormId(): string
    {
        return $this->bioFormId;
    }

    public function getIntegerFieldSearchFilterRequests(): array
    {
        return $this->integerFieldSearchFilterRequests;
    }

    public function getStringFieldSearchFilterRequests(): array
    {
        return $this->stringFieldSearchFilterRequests;
    }

    public function getTextAreaFieldSearchFilterRequests(): array
    {
        return $this->textAreaFieldSearchFilterRequests;
    }

    public function getSingleSelectFieldSearchFilterRequests(): array
    {
        return $this->singleSelectFieldSearchFilterRequests;
    }

    public function getMultiSelectFieldSearchFilterRequests(): array
    {
        return $this->multiSelectFieldSearchFilterRequests;
    }

    public function __construct(string $bioFormId)
    {
        $this->bioFormId = $bioFormId;
        $this->integerFieldSearchFilterRequests = [];
        $this->stringFieldSearchFilterRequests = [];
        $this->textAreaFieldSearchFilterRequests = [];
        $this->singleSelectFieldSearchFilterRequests = [];
        $this->multiSelectFieldSearchFilterRequests = [];
    }
    
    public function addIntegerFieldSearchFilterRequest(string $fieldId, int $comparisonType): void
    {
        $this->integerFieldSearchFilterRequests[$fieldId] = $comparisonType;
    }
    
    public function addStringFieldSearchFilterRequest(string $fieldId, int $comparisonType): void
    {
        $this->stringFieldSearchFilterRequests[$fieldId] = $comparisonType;
    }
    
    public function addTextAreaFieldSearchFilterRequest(string $fieldId, int $comparisonType): void
    {
        $this->textAreaFieldSearchFilterRequests[$fieldId] = $comparisonType;
    }
    
    public function addSingleSelectFieldSearchFilterRequest(string $fieldId, int $comparisonType): void
    {
        $this->singleSelectFieldSearchFilterRequests[$fieldId] = $comparisonType;
    }
    
    public function addMultiSelectFieldSearchFilterRequest(string $fieldId, int $comparisonType): void
    {
        $this->multiSelectFieldSearchFilterRequests[$fieldId] = $comparisonType;
    }

}
