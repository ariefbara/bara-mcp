<?php

namespace Firm\Domain\Task\InProgram;

use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Task\Dependency\Firm\Program\Participant\NoteRepository;
use Firm\Domain\Task\Dependency\Firm\Program\ParticipantRepository;

class SubmitNote implements TaskInProgram
{

    /**
     * 
     * @var NoteRepository
     */
    protected $noteRepository;

    /**
     * 
     * @var ParticipantRepository
     */
    protected $participantRepository;

    public function __construct(NoteRepository $noteRepository, ParticipantRepository $participantRepository)
    {
        $this->noteRepository = $noteRepository;
        $this->participantRepository = $participantRepository;
    }

    /**
     * 
     * @param Program $program
     * @param SubmitNotePayload $payload
     * @return void
     */
    public function execute(Program $program, $payload): void
    {
        
    }

}
