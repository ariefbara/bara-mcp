<?php

namespace User\Domain\Model\Personnel;

use Doctrine\Common\Collections\ArrayCollection;
use Resources\ {
    Exception\RegularException,
    Uuid
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use User\Domain\ {
    DependencyModel\Firm\Program,
    DependencyModel\Firm\Program\EvaluationPlan,
    DependencyModel\Firm\Program\Participant,
    Model\Personnel,
    Model\Personnel\Coordinator\EvaluationReport
};

class Coordinator
{

    /**
     *
     * @var Personnel
     */
    protected $personnel;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Program
     */
    protected $program;

    /**
     *
     * @var bool
     */
    protected $active;
    
    /**
     *
     * @var ArrayCollection
     */
    protected $evaluationReports;

    protected function __construct()
    {
        
    }

    public function submitEvaluationReportOfParticipant(
            Participant $participant, EvaluationPlan $evaluationPlan, FormRecordData $formRecordData): void
    {
        if (!$participant->isActiveParticipantOfProgram($this->program)) {
            $errorDetail = "forbidden: participant can't receive evaluation report";
            throw RegularException::forbidden($errorDetail);
        }
        if (!$evaluationPlan->isAnEnabledEvaluationPlanInProgram($this->program)) {
            $errorDetail = "forbidden: evaluation plan can't be used";
            throw RegularException::forbidden($errorDetail);
        }
        
        $p = function (EvaluationReport $evaluationReport) use ($participant, $evaluationPlan) {
            return $evaluationReport->aReportOfEvaluationPlanCorrespondWithParticipant($participant, $evaluationPlan);
        };
        
        if (!empty($evaluationReport = $this->evaluationReports->filter($p)->first())) {
            $evaluationReport->update($formRecordData);
        } else {
            $id = Uuid::generateUuid4();
            $evaluationReport = new EvaluationReport($this, $id, $participant, $evaluationPlan, $formRecordData);
            $this->evaluationReports->add($evaluationReport);
        }
    }
    
    
}
