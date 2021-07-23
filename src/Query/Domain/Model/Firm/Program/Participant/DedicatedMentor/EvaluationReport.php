<?php

namespace Query\Domain\Model\Firm\Program\Participant\DedicatedMentor;

use DateTimeImmutable;
use Query\Domain\Model\Firm\Program\EvaluationPlan;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor;
use Query\Domain\Model\Shared\FormRecord;

class EvaluationReport
{

    /**
     * 
     * @var DedicatedMentor
     */
    protected $dedicatedMentor;

    /**
     * 
     * @var EvaluationPlan
     */
    protected $evaluationPlan;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var DateTimeImmutable
     */
    protected $modifiedTime;

    /**
     * 
     * @var bool
     */
    protected $cancelled;
    
    /**
     * 
     * @var FormRecord
     */
    protected $formRecord;
    
    public function getDedicatedMentor(): DedicatedMentor
    {
        return $this->dedicatedMentor;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEvaluationPlan(): EvaluationPlan
    {
        return $this->evaluationPlan;
    }

    public function getFormRecord(): FormRecord
    {
        return $this->formRecord;
    }

    public function getModifiedTimeString(): string
    {
        return $this->modifiedTime->format('Y-m-d H:i:s');
    }

    public function isCancelled(): bool
    {
        return $this->cancelled;
    }

        
    protected function __construct()
    {
        
    }

}
