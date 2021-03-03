<?php

namespace Firm\Application\Service\Coordinator;

class ApproveOKRPeriod
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
                ->approveOKRPeriod($okrPeriod);
        $this->coordinatorRepository->update();
    }


}
