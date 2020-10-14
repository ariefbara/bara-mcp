<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation;

use Participant\Application\Service\ {
    Firm\Client\TeamMembershipRepository,
    Participant\ParticipantRepository,
    Participant\ViewLearningMaterialActivityLogRepository
};

class LogViewLearningMaterialActivity
{

    /**
     *
     * @var ViewLearningMaterialActivityLogRepository
     */
    protected $viewLearningMaterialActivityLogRepository;

    /**
     *
     * @var TeamMembershipRepository
     */
    protected $teamMembershipRepository;

    /**
     *
     * @var ParticipantRepository
     */
    protected $participantRepository;

    public function __construct(ViewLearningMaterialActivityLogRepository $viewLearningMaterialActivityLogRepository,
            TeamMembershipRepository $teamMembershipRepository, ParticipantRepository $participantRepository)
    {
        $this->viewLearningMaterialActivityLogRepository = $viewLearningMaterialActivityLogRepository;
        $this->teamMembershipRepository = $teamMembershipRepository;
        $this->participantRepository = $participantRepository;
    }

    public function execute(string $teamMemberId, string $participantId, string $learningMaterialId): void
    {
        $id = $this->viewLearningMaterialActivityLogRepository->nextIdentity();
        $participant = $this->participantRepository->ofId($participantId);
        $viewLearningMaterialActivityLog = $this->teamMembershipRepository->aTeamMembershipById($teamMemberId)
                ->logViewLearningMaterialActivity($id, $participant, $learningMaterialId);
        
        $this->viewLearningMaterialActivityLogRepository->add($viewLearningMaterialActivityLog);
    }

}
