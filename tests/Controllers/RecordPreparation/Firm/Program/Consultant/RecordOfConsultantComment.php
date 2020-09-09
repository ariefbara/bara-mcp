<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Consultant;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\Participant\Worksheet\RecordOfComment,
    Firm\Program\RecordOfConsultant,
    Record
};

class RecordOfConsultantComment implements Record
{
    /**
     *
     * @var RecordOfConsultant
     */
    public $consultant;
    /**
     *
     * @var RecordOfComment
     */
    public $comment;
    public $id;
    
    public function __construct(RecordOfConsultant $consultant, RecordOfComment $comment)
    {
        $this->consultant = $consultant;
        $this->comment = $comment;
        $this->id = $this->comment->id;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Consultant_id" => $this->consultant->id,
            "Comment_id" => $this->comment->id,
            "id" => $this->id,
        ];
    }

}
