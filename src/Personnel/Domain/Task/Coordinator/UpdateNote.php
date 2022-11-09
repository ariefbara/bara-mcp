<?php

namespace Personnel\Domain\Task\Coordinator;

use Personnel\Domain\Model\Firm\Personnel\Coordinator;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Coordinator\CoordinatorNoteRepository;

class UpdateNote implements CoordinatorTask
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
     * @param Coordinator $coordinator
     * @param UpdateNotePayload $payload
     * @return void
     */
    public function execute(Coordinator $coordinator, $payload): void
    {
        $coordinatorNote = $this->coordinatorNoteRepository->ofId($payload->getCoordinatorNoteId());
        $coordinatorNote->assertManageableByCoordinator($coordinator);
        $coordinatorNote->update($payload->getContent());
    }

}
