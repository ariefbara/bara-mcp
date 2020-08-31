<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant\Worksheet;

use Tests\Controllers\RecordPreparation\{
    Firm\Program\Participant\RecordOfWorksheet,
    Record
};

class RecordOfParticipantComment implements Record
{

    /**
     *
     * @var RecordOfWorksheet
     */
    public $worksheet;

    /**
     *
     * @var RecordOfComment
     */
    public $comment;
    public $id;
    
    public function __construct(RecordOfWorksheet $worksheet, RecordOfComment $comment)
    {
        $this->worksheet = $worksheet;
        $this->comment = $comment;
        $this->id = $comment->id;
    }

    public function toArrayForDbEntry()
    {
        return [
            'Worksheet_id' => $this->worksheet->id,
            'Comment_id' => $this->comment->id,
            'id' => $this->id,
        ];
    }

}
