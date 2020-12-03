<?php

namespace Query\Domain\Model\Firm\Program\EvaluationPlan;

use Query\Domain\Model\{
    Firm\Program\Coordinator,
    Firm\Program\EvaluationPlan,
    Firm\Program\Participant,
    Shared\ContainFormRecordInterface,
    Shared\FormRecord
};

class EvaluationReport implements ContainFormRecordInterface
{

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
     * @var Coordinator
     */
    protected $coordinator;

    /**
     *
     * @var Participant
     */
    protected $participant;

    /**
     *
     * @var FormRecord
     */
    protected $formRecord;

    function getEvaluationPlan(): EvaluationPlan
    {
        return $this->evaluationPlan;
    }

    function getCoordinator(): Coordinator
    {
        return $this->coordinator;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getParticipant(): Participant
    {
        return $this->participant;
    }

    protected function __construct()
    {
        
    }

    public function getSubmitTimeString(): string
    {
        return $this->formRecord->getSubmitTimeString();
    }

    public function getUnremovedAttachmentFieldRecords()
    {
        return $this->formRecord->getUnremovedAttachmentFieldRecords();
    }

    public function getUnremovedIntegerFieldRecords()
    {
        return $this->formRecord->getUnremovedIntegerFieldRecords();
    }

    public function getUnremovedMultiSelectFieldRecords()
    {
        return $this->formRecord->getUnremovedMultiSelectFieldRecords();
    }

    public function getUnremovedSingleSelectFieldRecords()
    {
        return $this->formRecord->getUnremovedSingleSelectFieldRecords();
    }

    public function getUnremovedStringFieldRecords()
    {
        return $this->formRecord->getUnremovedStringFieldRecords();
    }

    public function getUnremovedTextAreaFieldRecords()
    {
        return $this->formRecord->getUnremovedTextAreaFieldRecords();
    }

}
