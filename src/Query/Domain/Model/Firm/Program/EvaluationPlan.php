<?php

namespace Query\Domain\Model\Firm\Program;

use Query\Domain\Model\Firm\FeedbackForm;
use Query\Domain\Model\Firm\Program;

class EvaluationPlan
{

    /**
     *
     * @var Program
     */
    protected $program;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var int
     */
    protected $interval;

    /**
     *
     * @var bool
     */
    protected $disabled;

    /**
     *
     * @var FeedbackForm
     */
    protected $reportForm;
    
    /**
     * 
     * @var Mission|null
     */
    protected $mission;

    function getProgram(): Program
    {
        return $this->program;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getName(): string
    {
        return $this->name;
    }

    function getInterval(): int
    {
        return $this->interval;
    }

    public function getMission(): ?Mission
    {
        return $this->mission;
    }

    function isDisabled(): bool
    {
        return $this->disabled;
    }

    function getReportForm(): FeedbackForm
    {
        return $this->reportForm;
    }

    protected function __construct()
    {
        
    }

}
