<?php

namespace Firm\Application\Service\Coordinator;

class QualifyParticipant
{

    /**
     * 
     * @var ParticipantRepository
     */
    protected $participantRepository;

    /**
     * 
     * @var CoordinatorRepository
     */
    protected $coordinatorRepository;

    function __construct(ParticipantRepository $participantRepository, CoordinatorRepository $coordinatorRepository)
    {
        $this->participantRepository = $participantRepository;
        $this->coordinatorRepository = $coordinatorRepository;
    }
    
    public function execute(string $firmId, string $personnelId, string $programId, string $participantId): void
    {
        $participant = $this->participantRepository->ofId($participantId);
        $this->coordinatorRepository->aCoordinatorCorrespondWithProgram($firmId, $personnelId, $programId)
                ->qualifyParticipant($participant);
        $this->participantRepository->update();
    }

}
