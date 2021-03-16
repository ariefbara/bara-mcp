<?php

namespace Firm\Application\Service\Coordinator;

class RejectOKRPeriod
{
    /**
     * 
     * @var CoordinatorRepository
     */
    protected $coordinatorRepository;

    /**
     * 
     * @var OKRPeriodRepository
     */
    protected $okrPeriodRepository;
    
    public function __construct(CoordinatorRepository $coordinatorRepository, OKRPeriodRepository $okrPeriodRepository)
    {
        $this->coordinatorRepository = $coordinatorRepository;
        $this->okrPeriodRepository = $okrPeriodRepository;
    }
    
    public function execute(string $firmId, string $personnelId, string $programId, string $okrPeriodId): void
    {
        $okrPeriod = $this->okrPeriodRepository->ofId($okrPeriodId);
        $this->coordinatorRepository->aCoordinatorCorrespondWithProgram($firmId, $personnelId, $programId)
                ->rejectOKRPeriod($okrPeriod);
        $this->coordinatorRepository->update();
    }

}
