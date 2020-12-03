<?php

namespace User\Domain\Model\Personnel\Coordinator;

use SharedContext\Domain\Model\SharedEntity\{
    FormRecord,
    FormRecordData
};
use User\Domain\{
    DependencyModel\Firm\Program\EvaluationPlan,
    DependencyModel\Firm\Program\Participant,
    Model\Personnel\Coordinator
};

class EvaluationReport
{

    /**
     *
     * @var Coordinator
     */
    protected $coordinator;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Participant
     */
    protected $participant;

    /**
     *
     * @var EvaluationPlan
     */
    protected $evaluationPlan;

    /**
     *
     * @var FormRecord
     */
    protected $formRecord;

    function __construct(
            Coordinator $coordinator, string $id, Participant $participant, EvaluationPlan $evaluationPlan,
            FormRecordData $formRecordData)
    {
        $this->coordinator = $coordinator;
        $this->id = $id;
        $this->participant = $participant;
        $this->evaluationPlan = $evaluationPlan;
        $this->formRecord = $this->evaluationPlan->createFormRecord($id, $formRecordData);
    }

    public function update(FormRecordData $formRecordData): void
    {
        $this->formRecord->update($formRecordData);
    }

    public function aReportOfEvaluationPlanCorrespondWithParticipant(
            Participant $participant, EvaluationPlan $evaluationPlan): bool
    {
        return $this->participant === $participant && $this->evaluationPlan === $evaluationPlan;
    }

}
