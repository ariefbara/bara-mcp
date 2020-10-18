<?php

namespace Participant\Application\Service\Participant;

class LogViewLearningMaterialActivity
{

    /**
     *
     * @var ViewLearningMaterialActivityLogRepository
     */
    protected $viewLearningMaterialActivityLogRepository;

    /**
     *
     * @var ParticipantRepository
     */
    protected $participantRepository;

    public function __construct(ViewLearningMaterialActivityLogRepository $viewLearningMaterialActivityLogRepository,
            ParticipantRepository $participantRepository)
    {
        $this->viewLearningMaterialActivityLogRepository = $viewLearningMaterialActivityLogRepository;
        $this->participantRepository = $participantRepository;
    }

    public function execute(string $participantId, string $learningMaterialId): void
    {
        $id = $this->viewLearningMaterialActivityLogRepository->nextIdentity();
        $viewLearningMaterialActivityLog = $this->participantRepository->ofId($participantId)
                ->logViewLearningMaterialActivity($id, $learningMaterialId);
        $this->viewLearningMaterialActivityLogRepository->add($viewLearningMaterialActivityLog);
    }

}
