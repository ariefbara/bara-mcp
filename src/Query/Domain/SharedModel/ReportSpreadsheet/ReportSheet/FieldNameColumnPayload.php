<?php

namespace Query\Domain\SharedModel\ReportSpreadsheet\ReportSheet;

class FieldNameColumnPayload
{

    /**
     * 
     * @var ?string
     */
    protected $fieldName;

    /**
     * 
     * @var ?int
     */
    protected $colNumber;

    public function getFieldName(): ?string
    {
        return $this->fieldName;
    }

    public function getColNumber(): ?int
    {
        return $this->colNumber;
    }

    public function __construct(?string $fieldName, ?int $colNumber)
    {
        $this->fieldName = $fieldName;
        $this->colNumber = $colNumber;
    }

}
