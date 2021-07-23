<?php

namespace Personnel\Domain\Task\DedicatedMentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\DedicatedMentor;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ITaskExecutableByDedicatedMentor;
use Personnel\Domain\Task\Dependency\Firm\Program\EvaluationPlanRepository;

class CancelEvaluationReport implements ITaskExecutableByDedicatedMentor
{

    /**
     * 
     * @var EvaluationReportRepository
     */
    protected $evaluationReportRepository;

    /**
     * 
     * @var string
     */
    protected $id;

    public function __construct(EvaluationReportRepository $evaluationReportRepository, string $id)
    {
        $this->evaluationReportRepository = $evaluationReportRepository;
        $this->id = $id;
    }

    public function execute(DedicatedMentor $dedicatedMentor): void
    {
        $evaluationReport = $this->evaluationReportRepository->ofId($this->id);
        $evaluationReport->assertManageableByDedicatedMentor($dedicatedMentor);
        $evaluationReport->cancel();
    }

}
