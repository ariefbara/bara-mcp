<?php

namespace Personnel\Domain\Task\DedicatedMentor;

use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class EvaluationReportPayload
{

    /**
     * 
     * @var string
     */
    protected $evaluationPlanId;

    /**
     * 
     * @var FormRecordData
     */
    protected $formRecordData;

    public function getEvaluationPlanId(): string
    {
        return $this->evaluationPlanId;
    }

    public function getFormRecordData(): FormRecordData
    {
        return $this->formRecordData;
    }

    public function __construct(string $evaluationPlanId, FormRecordData $formRecordData)
    {
        $this->evaluationPlanId = $evaluationPlanId;
        $this->formRecordData = $formRecordData;
    }

}
