<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant\Worksheet;

use Tests\Controllers\RecordPreparation\{
    Firm\Program\Participant\RecordOfWorksheet,
    Firm\Program\RecordOfConsultant,
    Record
};

class RecordOfConsultantComment implements Record
{

    /**
     *
     * @var RecordOfWorksheet
     */
    public $worksheet;

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
    
    public function __construct(RecordOfWorksheet $worksheet, RecordOfConsultant $consultant, RecordOfComment $comment)
    {
        $this->worksheet = $worksheet;
        $this->consultant = $consultant;
        $this->comment = $comment;
        $this->id = $comment->id;
    }

    public function toArrayForDbEntry()
    {
        return [
            'Worksheet_id' => $this->worksheet->id,
            'Consultant_id' => $this->consultant->id,
            'Comment_id' => $this->comment->id,
            'id' => $this->id,
        ];
    }

}
