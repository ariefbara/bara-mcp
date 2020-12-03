<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\Program\EvaluationPlanData;

class UpdateEvaluationPlan
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
     * @var FeedbackFormRepository
     */
    protected $feedbackFormRepository;

    function __construct(
            EvaluationPlanRepository $evaluationPlanRepository, ManagerRepository $managerRepository,
            FeedbackFormRepository $feedbackFormRepository)
    {
        $this->evaluationPlanRepository = $evaluationPlanRepository;
        $this->managerRepository = $managerRepository;
        $this->feedbackFormRepository = $feedbackFormRepository;
    }

    public function execute(
            string $firmId, string $managerId, string $evaluationPlanId, EvaluationPlanData $evaluationPlanData,
            string $feedbackFormId): void
    {
        $evaluationPlan = $this->evaluationPlanRepository->ofId($evaluationPlanId);
        $feedbackForm = $this->feedbackFormRepository->aFeedbackFormOfId($feedbackFormId);

        $this->managerRepository->aManagerInFirm($firmId, $managerId)
                ->updateEvaluationPlan($evaluationPlan, $evaluationPlanData, $feedbackForm);
        
        $this->evaluationPlanRepository->update();
    }

}
