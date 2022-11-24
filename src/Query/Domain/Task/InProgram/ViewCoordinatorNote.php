<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByConsultant;
use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByCoordinator;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Dependency\Firm\Program\Coordinator\CoordinatorNoteRepository;

class ViewCoordinatorNote implements ProgramTaskExecutableByConsultant, ProgramTaskExecutableByCoordinator
{

    /**
     * 
     * @var CoordinatorNoteRepository
     */
    protected $coordinatorNoteRepository;

    public function __construct(CoordinatorNoteRepository $coordinatorNoteRepository)
    {
        $this->coordinatorNoteRepository = $coordinatorNoteRepository;
    }

    /**
     * 
     * @param string $programId
     * @param CommonViewDetailPayload $payload
     * @return void
     */
    public function execute(string $programId, $payload): void
    {
        $payload->result = $this->coordinatorNoteRepository->aCoordinatorNoteInProgram($programId, $payload->getId());
    }

}
