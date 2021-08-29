<?php

namespace Query\Domain\Model\Firm\Program\EvaluationPlan\SummaryTable;

use Query\Domain\SharedModel\IHeaderColumn;

class StaticHeaderColumn implements IHeaderColumn
{

    /**
     * 
     * @var int
     */
    protected $colNumber;

    /**
     * 
     * @var string
     */
    protected $label;

    public function getColNumber(): int
    {
        return $this->colNumber;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function __construct(int $colNumber, string $label)
    {
        $this->colNumber = $colNumber;
        $this->label = $label;
    }

    public function toArray(): array
    {
        return [
            'colNumber' => $this->colNumber,
            'label' => $this->label,
        ];
    }

}
