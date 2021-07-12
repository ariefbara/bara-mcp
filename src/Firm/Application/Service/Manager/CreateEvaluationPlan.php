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

    /**
     * 
     * @var MissionRepository
     */
    protected $missionRepository;

    public function __construct(
            EvaluationPlanRepository $evaluationPlanRepository, ManagerRepository $managerRepository,
            ProgramRepository $programRepository, FeedbackFormRepository $feedbackFormRepository,
            MissionRepository $missionRepository)
    {
        $this->evaluationPlanRepository = $evaluationPlanRepository;
        $this->managerRepository = $managerRepository;
        $this->programRepository = $programRepository;
        $this->feedbackFormRepository = $feedbackFormRepository;
        $this->missionRepository = $missionRepository;
    }

    public function execute(
            string $firmId, string $managerId, string $programId, EvaluationPlanData $evaluationPlanData,
            string $feedbackFormId, ?string $missionId): string
    {
        $program = $this->programRepository->aProgramOfId($programId);
        $id = $this->evaluationPlanRepository->nextIdentity();
        $reportForm = $this->feedbackFormRepository->aFeedbackFormOfId($feedbackFormId);
        $mission = empty($missionId)? null: $this->missionRepository->aMissionOfId($missionId);

        $evaluationPlan = $this->managerRepository->aManagerInFirm($firmId, $managerId)
                ->createEvaluationPlanInProgram($program, $id, $evaluationPlanData, $reportForm, $mission);
        $this->evaluationPlanRepository->add($evaluationPlan);
        return $id;
    }

}
