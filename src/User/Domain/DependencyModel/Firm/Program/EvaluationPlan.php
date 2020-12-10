<?php

namespace User\Domain\DependencyModel\Firm\Program;

use SharedContext\Domain\Model\SharedEntity\{
    FormRecord,
    FormRecordData
};
use User\Domain\DependencyModel\Firm\{
    FeedbackForm,
    Program
};

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

    protected function __construct()
    {
        
    }

    public function isAnEnabledEvaluationPlanInProgram(Program $program): bool
    {
        return !$this->disabled && $this->program === $program;
    }

    public function createFormRecord(string $formRecordId, FormRecordData $formRecordData): FormRecord
    {
        return $this->reportForm->createFormRecord($formRecordId, $formRecordData);
    }

}
