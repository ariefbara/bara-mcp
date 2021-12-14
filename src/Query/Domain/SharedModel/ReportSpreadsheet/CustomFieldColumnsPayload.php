<?php

namespace Query\Domain\SharedModel\ReportSpreadsheet;

class CustomFieldColumnsPayload
{

    /**
     * 
     * @var iterable
     */
    protected $inspectedFieldList = [];

    public function getInspectedFieldList(): iterable
    {
        return $this->inspectedFieldList;
    }

    public function __construct()
    {
        
    }
    
    public function inspectField(string $fieldId, int $colNumber): self
    {
        $this->inspectedFieldList[$colNumber] = $fieldId;
        return $this;
    }

}
