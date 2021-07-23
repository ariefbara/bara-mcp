<?php

namespace Personnel\Domain\Task\DedicatedMentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\DedicatedMentor;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ITaskExecutableByDedicatedMentor;
use Personnel\Domain\Task\Dependency\Firm\Program\EvaluationPlanRepository;
use Resources\Application\Event\Dispatcher;

class SubmitEvaluationReportTask implements ITaskExecutableByDedicatedMentor
{

    /**
     * 
     * @var EvaluationPlanRepository
     */
    protected $evaluationPlanRepository;

    /**
     * 
     * @var EvaluationReportPayload
     */
    protected $payload;

    /**
     * 
     * @var Dispatcher
     */
    protected $dispatcher;

    public function __construct(EvaluationPlanRepository $evaluationPlanRepository, EvaluationReportPayload $payload,
            Dispatcher $dispatcher)
    {
        $this->evaluationPlanRepository = $evaluationPlanRepository;
        $this->payload = $payload;
        $this->dispatcher = $dispatcher;
    }

    public function execute(DedicatedMentor $dedicatedMentor): void
    {
        $evaluationPlan = $this->evaluationPlanRepository->ofId($this->payload->getEvaluationPlanId());
        $dedicatedMentor->submitEvaluationReport($evaluationPlan, $this->payload->getFormRecordData());
        $this->dispatcher->dispatch($dedicatedMentor);
    }

}
