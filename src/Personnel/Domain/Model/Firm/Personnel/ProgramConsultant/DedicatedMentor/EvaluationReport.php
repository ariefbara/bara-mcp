<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\DedicatedMentor;

use Config\EventList;
use DateTimeImmutable;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\DedicatedMentor;
use Personnel\Domain\Model\Firm\Program\EvaluationPlan;
use Resources\DateTimeImmutableBuilder;
use Resources\Domain\Event\CommonEvent;
use Resources\Domain\Model\EntityContainEvents;
use Resources\Exception\RegularException;
use SharedContext\Domain\Model\SharedEntity\FormRecord;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class EvaluationReport extends EntityContainEvents
{

    /**
     * 
     * @var DedicatedMentor
     */
    protected $dedicatedMentor;

    /**
     * 
     * @var string
     */
    protected $id;

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
    
    public function assertManageableByDedicatedMentor(DedicatedMentor $dedicatedMentor): void
    {
        if ($this->dedicatedMentor !== $dedicatedMentor) {
            throw RegularException::forbidden('forbidden: evaluation report is not manageable');
        }
    }

    public function __construct(
            DedicatedMentor $dedicatedMentor, string $id, EvaluationPlan $evaluationPlan, FormRecordData $formRecordData)
    {
        $this->dedicatedMentor = $dedicatedMentor;
        $this->id = $id;
        $this->evaluationPlan = $evaluationPlan;
        $this->formRecord = $this->evaluationPlan->createFormRecord($this->id, $formRecordData);
        $this->modifiedTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        $this->cancelled = false;
        
        $this->recordEvent(new CommonEvent(EventList::COMMON_ENTITY_CREATED, $this->id));
    }
    
    public function update(FormRecordData $formRecordData): void
    {
        if ($this->cancelled) {
            throw RegularException::forbidden('forbidden: evaluation report already cancelled');
        }
        $this->formRecord->update($formRecordData);
        $this->modifiedTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        
        $this->recordEvent(new CommonEvent(EventList::COMMON_ENTITY_CREATED, $this->id));
    }
    
    public function cancel(): void
    {
        if ($this->cancelled) {
            throw RegularException::forbidden('forbidden: evaluation report already cancelled');
        }
        $this->cancelled = true;
    }
    
    public function isActiveReportCorrespondWithEvaluationPlan(EvaluationPlan $evaluationPlan): bool
    {
        return !$this->cancelled && $this->evaluationPlan === $evaluationPlan;
    }

}
