<?php

namespace Firm\Domain\Model\Firm\Program\Participant;

use Firm\Domain\Model\Firm\Program\Participant;

class Note
{

    /**
     * 
     * @var Participant
     */
    protected $participant;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var string
     */
    protected $contents;

    /**
     * 
     * @var bool
     */
    protected $visibleToParticipant;

    public function __construct(Participant $participant, string $id, NoteData $data)
    {
//        $this->participant = $participant;
//        $this->id = $id;
//        $this->contents = $contents;
//        $this->visibleToParticipant = $visibleToParticipant;
    }

    public function update(NoteData $data): void
    {
        
    }

}
