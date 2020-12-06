<?php

namespace Firm\Application\Service\Coordinator;

use Firm\Domain\Model\Firm\Program\Participant\EvaluationData;

class EvaluateParticipant
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
            string $firmId, string $personnelId, string $programId, string $participantId, string $evaluationPlanId,
            EvaluationData $evaluationData): void
    {
        $participant = $this->participantRepository->ofId($participantId);
        $evaluationPlan = $this->evaluationPlanRepository->ofId($evaluationPlanId);
        
        $this->coordinatorRepository->aCoordinatorCorrespondWithProgram($firmId, $personnelId, $programId)
                ->evaluateParticipant($participant, $evaluationPlan, $evaluationData);
        
        $this->participantRepository->update();
    }

}
