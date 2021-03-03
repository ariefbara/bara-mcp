<?php

namespace Query\Application\Service\Coordinator;

use Query\Domain\Model\Firm\Program\Participant\OKRPeriod;

class ViewOKRPeriod
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

    /**
     * 
     * @param string $firmId
     * @param string $personnelId
     * @param string $programId
     * @param string $participantId
     * @param int $page
     * @param int $pageSize
     * @return OKRPeriod[]
     */
    public function showAll(
            string $firmId, string $personnelId, string $programId, string $participantId, int $page, int $pageSize)
    {
        return $this->coordinatorRepository->aCoordinatorCorrespondWithProgram($firmId, $personnelId, $programId)
                        ->viewAllOKRPeriodBelongsToParticipant(
                                $this->okrPeriodRepository, $participantId, $page, $pageSize);
    }

    public function showById(string $firmId, string $personnelId, string $programId, string $okrPeriodId): OKRPeriod
    {
        return $this->coordinatorRepository->aCoordinatorCorrespondWithProgram($firmId, $personnelId, $programId)
                        ->viewOKRPeriod($this->okrPeriodRepository, $okrPeriodId);
    }

}
