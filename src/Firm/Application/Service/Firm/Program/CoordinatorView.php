<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Domain\Model\Firm\Program\Coordinator;

class CoordinatorView
{
    /**
     *
     * @var CoordinatorRepository
     */
    protected $coordinatorRepository;
    
    function __construct(CoordinatorRepository $coordinatorRepository)
    {
        $this->coordinatorRepository = $coordinatorRepository;
    }
    
    public function showById(ProgramCompositionId $programCompositionId, string $coordinatorId): Coordinator
    {
        return $this->coordinatorRepository->ofId($programCompositionId, $coordinatorId);
    }
    
    /**
     * 
     * @param ProgramCompositionId $programCompositionId
     * @param int $page
     * @param int $pageSize
     * @return Coordinator[]
     */
    public function showAll(ProgramCompositionId $programCompositionId, int $page, int $pageSize)
    {
        return $this->coordinatorRepository->all($programCompositionId, $page, $pageSize);
    }

}
