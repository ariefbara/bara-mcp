<?php

namespace Firm\Application\Service\Coordinator;

class GivePassEvaluationToParticipant
{

    /**
     *
     * @var CoordinatorRepository
     */
    protected $coordinatorRepository;

    /**
     *
     * @var ParticipantRepository
     */
    protected $participantRepository;

    /**
     *
     * @var EvaluationPlanRepository
     */
    protected $evaluationPlanRepository;

    function __construct(
            CoordinatorRepository $coordinatorRepository, ParticipantRepository $participantRepository,
            EvaluationPlanRepository $evaluationPlanRepository)
    {
        $this->coordinatorRepository = $coordinatorRepository;
        $this->participantRepository = $participantRepository;
        $this->evaluationPlanRepository = $evaluationPlanRepository;
    }

    public function execute(
            string $firmId, string $personnelId, string $programId, string $participantId, string $evaluationPlanId): void
    {
        $participant = $this->participantRepository->ofId($participantId);
        $evaluationPlan = $this->evaluationPlanRepository->ofId($evaluationPlanId);
        
        $this->coordinatorRepository->aCoordinatorCorrespondWithProgram($firmId, $personnelId, $programId)
                ->givePassEvaluationToParticipant($participant, $evaluationPlan);
        $this->participantRepository->update();
    }

}
