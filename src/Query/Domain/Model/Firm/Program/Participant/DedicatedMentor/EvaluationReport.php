<?php

namespace Query\Domain\Model\Firm\Program\Participant\DedicatedMentor;

use DateTimeImmutable;
use Query\Domain\Model\Firm\Program\EvaluationPlan;
use Query\Domain\Model\Firm\Program\EvaluationPlan\EvaluationPlanReportSummary;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor;
use Query\Domain\Model\Shared\ContainFormRecordInterface;
use Query\Domain\Model\Shared\FormRecord;
use Query\Domain\Model\Shared\FormRecord\AttachmentFieldRecord;
use Query\Domain\Model\Shared\FormRecord\IntegerFieldRecord;
use Query\Domain\Model\Shared\FormRecord\MultiSelectFieldRecord;
use Query\Domain\Model\Shared\FormRecord\SingleSelectFieldRecord;
use Query\Domain\Model\Shared\FormRecord\StringFieldRecord;
use Query\Domain\Model\Shared\FormRecord\TextAreaFieldRecord;

class EvaluationReport implements ContainFormRecordInterface
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

    function getSubmitTimeString(): ?string
    {
        return $this->formRecord->getSubmitTimeString();
    }

    /**
     * 
     * @return IntegerFieldRecord[]
     */
    function getUnremovedIntegerFieldRecords()
    {
        return $this->formRecord->getUnremovedIntegerFieldRecords();
    }

    /**
     * 
     * @return StringFieldRecord[]
     */
    function getUnremovedStringFieldRecords()
    {
        return $this->formRecord->getUnremovedStringFieldRecords();
    }

    /**
     * 
     * @return TextAreaFieldRecord[]
     */
    function getUnremovedTextAreaFieldRecords()
    {
        return $this->formRecord->getUnremovedTextAreaFieldRecords();
    }

    /**
     * 
     * @return SingleSelectFieldRecord[]
     */
    function getUnremovedSingleSelectFieldRecords()
    {
        return $this->formRecord->getUnremovedSingleSelectFieldRecords();
    }

    /**
     * 
     * @return MultiSelectFieldRecord[]
     */
    function getUnremovedMultiSelectFieldRecords()
    {
        return $this->formRecord->getUnremovedMultiSelectFieldRecords();
    }

    /**
     * 
     * @return AttachmentFieldRecord[]
     */
    function getUnremovedAttachmentFieldRecords()
    {
        return $this->formRecord->getUnremovedAttachmentFieldRecords();
    }
    
    public function evaluationPlanEquals(EvaluationPlan $evaluationPlan): bool
    {
        return $this->evaluationPlan === $evaluationPlan;
    }
    
    public function createEvaluationPlanReportSummary(): EvaluationPlan\EvaluationPlanReportSummary
    {
        return new EvaluationPlanReportSummary($this->evaluationPlan, $this);
    }
    
    public function toArrayOfSummaryTableEntry(): array
    {
        $identificationEntry = [
            $this->dedicatedMentor->getParticipantName(),
            $this->dedicatedMentor->getMentorName(),
        ];
        return array_merge($identificationEntry , $this->evaluationPlan->generateSummaryTableEntryFromRecord($this->formRecord));
    }
    
}
