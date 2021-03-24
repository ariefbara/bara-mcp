<?php

namespace Firm\Application\Service\Coordinator;

class CancelMentorDedication
{

    /**
     * 
     * @var CoordinatorRepository
     */
    protected $coordinatorRepository;

    /**
     * 
     * @var DedicatedMentorRepository
     */
    protected $dedicatedMentorRepository;

    public function __construct(
            CoordinatorRepository $coordinatorRepository, DedicatedMentorRepository $dedicatedMentorRepository)
    {
        $this->coordinatorRepository = $coordinatorRepository;
        $this->dedicatedMentorRepository = $dedicatedMentorRepository;
    }
    
    public function execute(string $firmId, string $personnelId, string $programId, string $dedicatedMentorId): void
    {
        $dedicatedMentor = $this->dedicatedMentorRepository->ofId($dedicatedMentorId);
        $this->coordinatorRepository->aCoordinatorCorrespondWithProgram($firmId, $personnelId, $programId)
                ->cancelMentorDedication($dedicatedMentor);
        $this->coordinatorRepository->update();
    }

}
