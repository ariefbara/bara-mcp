<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\Participant\Worksheet\RecordOfComment,
    Firm\Program\RecordOfParticipant,
    Record
};

class RecordOfParticipantComment implements Record
{
    /**
     *
     * @var RecordOfParticipant
     */
    public $participant;
    /**
     *
     * @var RecordOfComment
     */
    public $comment;
    public $id;
    
    function __construct(RecordOfParticipant $participant, RecordOfComment $comment)
    {
        $this->participant = $participant;
        $this->comment = $comment;
        $this->id = $comment->id;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Participant_id" => $this->participant->id,
            "Comment_id" => $this->comment->id,
            "id" => $this->id,
        ];
    }

}
