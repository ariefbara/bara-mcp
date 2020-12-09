<?php

namespace Firm\Application\Service\Coordinator;

class ChangeConsultationSessionChannel
{

    /**
     * 
     * @var ConsultationSessionRepository
     */
    protected $consultationSessionRepository;

    /**
     * 
     * @var CoordinatorRepository
     */
    protected $coordinatorRepository;

    function __construct(ConsultationSessionRepository $consultationSessionRepository,
            CoordinatorRepository $coordinatorRepository)
    {
        $this->consultationSessionRepository = $consultationSessionRepository;
        $this->coordinatorRepository = $coordinatorRepository;
    }

    public function execute(
            string $firmId, string $personnelId, string $programId, string $consultationSessionId, ?string $media,
            ?string $address): void
    {
        $consultationSession = $this->consultationSessionRepository->ofId($consultationSessionId);
        $this->coordinatorRepository->aCoordinatorCorrespondWithProgram($firmId, $personnelId, $programId)
                ->changeConsultationSessionChannel($consultationSession, $media, $address);
        
        $this->consultationSessionRepository->update();
    }

}
