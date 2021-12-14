<?php

namespace Query\Domain\SharedModel\ReportSpreadsheet;

class ReportSheetPayload
{

    /**
     * 
     * @var bool|null
     */
    protected $evaluationInspected;

    /**
     * 
     * @var int|null
     */
    protected $evaluationColNumber;

    /**
     * 
     * @var bool|null
     */
    protected $evaluatorInspected;

    /**
     * 
     * @var int|null
     */
    protected $evaluatorColNumber;

    /**
     * 
     * @var bool|null
     */
    protected $evaluateeInspected;

    /**
     * 
     * @var int|null
     */
    protected $evaluateeColNumber;

    /**
     * 
     * @var bool|null
     */
    protected $submitTimeInspected;

    /**
     * 
     * @var int|null
     */
    protected $submitTimeColNumber;

    public function isEvaluationInspected(): ?bool
    {
        return $this->evaluationInspected;
    }

    public function isEvaluatorInspected(): ?bool
    {
        return $this->evaluatorInspected;
    }

    public function isEvaluateeInspected(): ?bool
    {
        return $this->evaluateeInspected;
    }

    public function isSubmitTimeInspected(): ?bool
    {
        return $this->submitTimeInspected;
    }

    public function getEvaluationColNumber(): ?int
    {
        return $this->evaluationColNumber;
    }

    public function getEvaluatorColNumber(): ?int
    {
        return $this->evaluatorColNumber;
    }

    public function getEvaluateeColNumber(): ?int
    {
        return $this->evaluateeColNumber;
    }

    public function getSubmitTimeColNumber(): ?int
    {
        return $this->submitTimeColNumber;
    }

    public function __construct()
    {
        
    }

    public function inspectEvaluation(int $colNumber): self
    {
        $this->evaluationInspected = true;
        $this->evaluationColNumber = $colNumber;
        return $this;
    }

    public function inspectEvaluator(int $colNumber): self
    {
        $this->evaluatorInspected = true;
        $this->evaluatorColNumber = $colNumber;
        return $this;
    }

    public function inspectEvaluatee(int $colNumber): self
    {
        $this->evaluateeInspected = true;
        $this->evaluateeColNumber = $colNumber;
        return $this;
    }

    public function inspectSubmitTime(int $colNumber): self
    {
        $this->submitTimeInspected = true;
        $this->submitTimeColNumber = $colNumber;
        return $this;
    }

}
