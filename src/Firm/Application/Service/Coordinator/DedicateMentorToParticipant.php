<?php

namespace Firm\Application\Service\Coordinator;

class DedicateMentorToParticipant
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
     * @var ConsultantRepository
     */
    protected $consultantRepository;

    public function __construct(
            CoordinatorRepository $coordinatorRepository, ParticipantRepository $participantRepository,
            ConsultantRepository $consultantRepository)
    {
        $this->coordinatorRepository = $coordinatorRepository;
        $this->participantRepository = $participantRepository;
        $this->consultantRepository = $consultantRepository;
    }

    public function execute(
            string $firmId, string $personnelId, string $programId, string $participantId, string $consultantId): string
    {
        $participant = $this->participantRepository->ofId($participantId);
        $consultant = $this->consultantRepository->aConsultantOfId($consultantId);
        $dedicatedMentorId = $this->coordinatorRepository->aCoordinatorCorrespondWithProgram($firmId, $personnelId, $programId)
                ->dedicateMentorToParticipant($participant, $consultant);
        $this->coordinatorRepository->update();
        return $dedicatedMentorId;
    }

}
