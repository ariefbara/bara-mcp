<?php

namespace Firm\Application\Service\Firm\Program;

class CoordinatorRemove
{
    protected $coordinatorRepository;
    
    function __construct(CoordinatorRepository $coordinatorRepository)
    {
        $this->coordinatorRepository = $coordinatorRepository;
    }
    
    public function execute(ProgramCompositionId $programCompositionId, string $coordinatorId): void
    {
        $this->coordinatorRepository->ofId($programCompositionId, $coordinatorId)
            ->remove();
        $this->coordinatorRepository->update();
    }

}
