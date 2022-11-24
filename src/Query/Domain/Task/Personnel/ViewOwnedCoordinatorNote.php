<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Dependency\Firm\Program\Coordinator\CoordinatorNoteRepository;

class ViewOwnedCoordinatorNote implements PersonnelTask
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
     * @param string $personnelId
     * @param CommonViewDetailPayload $payload
     * @return void
     */
    public function execute(string $personnelId, $payload): void
    {
        $payload->result = $this->coordinatorNoteRepository
                ->aCoordinatorNoteBelongsToPersonnel($personnelId, $payload->getId());
    }

}
