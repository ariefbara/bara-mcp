<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

use Doctrine\Common\Collections\ArrayCollection;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\DedicatedMentor\EvaluationReport;
use Personnel\Domain\Model\Firm\Program\EvaluationPlan;
use Resources\Domain\Model\EntityContainEvents;
use Resources\Exception\RegularException;
use Resources\Uuid;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class DedicatedMentor extends EntityContainEvents
{

    /**
     * 
     * @var ProgramConsultant
     */
    protected $consultant;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var bool
     */
    protected $cancelled;
    
    /**
     * 
     * @var ArrayCollection
     */
    protected $evaluationReports;

    protected function __construct()
    {
        
    }

    public function executeTask(ITaskExecutableByDedicatedMentor $task): void
    {
        if ($this->cancelled) {
            throw RegularException::forbidden('forbidden: only active dedicated mentor can make this request');
        }
        $task->execute($this);
    }

    public function submitEvaluationReport(EvaluationPlan $evaluationPlan, FormRecordData $formRecordData)
    {
        $this->consultant->verifyAssetUsable($evaluationPlan);
        
        $p = function (EvaluationReport $evaluationReport) use ($evaluationPlan) {
            return $evaluationReport->isActiveReportCorrespondWithEvaluationPlan($evaluationPlan);
        };
        $evaluationReport = $this->evaluationReports->filter($p)->first();
        if (!empty($evaluationReport)) {
            $evaluationReport->update($formRecordData);
        } else {
            $evaluationReport = new EvaluationReport($this, Uuid::generateUuid4(), $evaluationPlan, $formRecordData);
            $this->evaluationReports->add($evaluationReport);
        }
        
        $this->aggregateEventFrom($evaluationReport);
    }
    
}
