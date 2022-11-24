<?php

namespace Firm\Domain\Task\InProgram;

use Firm\Domain\Model\Firm\Program\Participant\NoteData;

class SubmitNotePayload
{

    /**
     * 
     * @var string
     */
    protected $participantId;

    /**
     * 
     * @var NoteData
     */
    protected $noteData;
    public $submittedNoteId;

    public function getParticipantId(): string
    {
        return $this->participantId;
    }

    public function getNoteData(): NoteData
    {
        return $this->noteData;
    }

    public function __construct(string $participantId, NoteData $noteData)
    {
        $this->participantId = $participantId;
        $this->noteData = $noteData;
    }

}
