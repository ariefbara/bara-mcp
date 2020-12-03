<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\Program\EvaluationPlanData;

class CreateEvaluationPlan
{

    /**
     *
     * @var EvaluationPlanRepository
     */
    protected $evaluationPlanRepository;

    /**
     *
     * @var ManagerRepository
     */
    protected $managerRepository;

    /**
     *
     * @var ProgramRepository
     */
    protected $programRepository;

    /**
     *
     * @var FeedbackFormRepository
     */
    protected $feedbackFormRepository;

    function __construct(
            EvaluationPlanRepository $evaluationPlanRepository, ManagerRepository $managerRepository,
            ProgramRepository $programRepository, FeedbackFormRepository $feedbackFormRepository)
    {
        $this->evaluationPlanRepository = $evaluationPlanRepository;
        $this->managerRepository = $managerRepository;
        $this->programRepository = $programRepository;
        $this->feedbackFormRepository = $feedbackFormRepository;
    }

    public function execute(
            string $firmId, string $managerId, string $programId, EvaluationPlanData $evaluationPlanData,
            string $feedbackFormId): string
    {
        $program = $this->programRepository->aProgramOfId($programId);
        $id = $this->evaluationPlanRepository->nextIdentity();
        $reportForm = $this->feedbackFormRepository->aFeedbackFormOfId($feedbackFormId);
        
        $evaluationPlan = $this->managerRepository->aManagerInFirm($firmId, $managerId)
                ->createEvaluationPlanInProgram($program, $id, $evaluationPlanData, $reportForm);
        $this->evaluationPlanRepository->add($evaluationPlan);
        return $id;
    }

}
