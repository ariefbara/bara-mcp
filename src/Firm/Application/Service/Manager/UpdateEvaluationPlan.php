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

    /**
     * 
     * @var MissionRepository
     */
    protected $missionRepository;

    public function __construct(
            EvaluationPlanRepository $evaluationPlanRepository, ManagerRepository $managerRepository,
            FeedbackFormRepository $feedbackFormRepository, MissionRepository $missionRepository)
    {
        $this->evaluationPlanRepository = $evaluationPlanRepository;
        $this->managerRepository = $managerRepository;
        $this->feedbackFormRepository = $feedbackFormRepository;
        $this->missionRepository = $missionRepository;
    }

    public function execute(
            string $firmId, string $managerId, string $evaluationPlanId, EvaluationPlanData $evaluationPlanData,
            string $feedbackFormId, ?string $missionId): void
    {
        $evaluationPlan = $this->evaluationPlanRepository->ofId($evaluationPlanId);
        $feedbackForm = $this->feedbackFormRepository->aFeedbackFormOfId($feedbackFormId);
        $mission = empty($missionId) ? null:  $this->missionRepository->aMissionOfId($missionId);

        $this->managerRepository->aManagerInFirm($firmId, $managerId)
                ->updateEvaluationPlan($evaluationPlan, $evaluationPlanData, $feedbackForm, $mission);

        $this->evaluationPlanRepository->update();
    }

}
