<?php

namespace User\Application\Service\Personnel\Coordinator;

use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class SubmitEvaluationReport
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

    function __construct(CoordinatorRepository $coordinatorRepository, ParticipantRepository $participantRepository,
            EvaluationPlanRepository $evaluationPlanRepository)
    {
        $this->coordinatorRepository = $coordinatorRepository;
        $this->participantRepository = $participantRepository;
        $this->evaluationPlanRepository = $evaluationPlanRepository;
    }

    public function execute(
            string $firmId, string $personnelId, string $coordinatorId, string $participantId, string $evaluationPlanId,
            FormRecordData $formRecordData): void
    {
        $participant = $this->participantRepository->ofId($participantId);
        $evaluationPlan = $this->evaluationPlanRepository->ofId($evaluationPlanId);
        
        $this->coordinatorRepository->aCoordinatorBelongsToPersonnel($firmId, $personnelId, $coordinatorId)
                ->submitEvaluationReportOfParticipant($participant, $evaluationPlan, $formRecordData);
        $this->coordinatorRepository->update();
    }

}
