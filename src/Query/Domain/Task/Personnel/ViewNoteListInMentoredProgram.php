<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Task\CommonViewListPayload;
use Query\Domain\Task\Dependency\NoteRepository;

class ViewNoteListInMentoredProgram implements PersonnelTask
{

    /**
     * 
     * @var NoteRepository
     */
    protected $noteRepository;

    public function __construct(NoteRepository $noteRepository)
    {
        $this->noteRepository = $noteRepository;
    }

    /**
     * 
     * @param string $personnelId
     * @param CommonViewListPayload $payload
     * @return void
     */
    public function execute(string $personnelId, $payload): void
    {
        $payload->result = $this->noteRepository
                ->allNotesInProgramsMentoredByPersonnel($personnelId, $payload->getFilter());
    }

}
