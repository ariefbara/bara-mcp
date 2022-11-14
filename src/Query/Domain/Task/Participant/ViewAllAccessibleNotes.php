<?php

namespace Query\Domain\Task\Participant;

use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Task\Dependency\NoteRepository;

class ViewAllAccessibleNotes implements ParticipantQueryTask
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
     * @param Participant $participant
     * @param ViewAllAccessibleNotesPayload $payload
     * @return void
     */
    public function execute(Participant $participant, $payload): void
    {
        $payload->result = $this->noteRepository
                ->allNoteAccessibleByParticipant($participant->getId(), $payload->getNoteFilter());
    }

}
