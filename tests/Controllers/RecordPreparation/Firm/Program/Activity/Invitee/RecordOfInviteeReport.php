<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Activity\Invitee;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\Activity\RecordOfInvitee,
    Record,
    Shared\RecordOfFormRecord
};

class RecordOfInviteeReport implements Record
{
    /**
     *
     * @var RecordOfInvitee
     */
    public $invitee;
    /**
     *
     * @var RecordOfFormRecord
     */
    public $formRecord;
    public $id;
    
    function __construct(RecordOfInvitee $invitee, RecordOfFormRecord $formRecord)
    {
        $this->invitee = $invitee;
        $this->formRecord = $formRecord;
        $this->id = $this->formRecord->id;
    }

    
    public function toArrayForDbEntry()
    {
        return [
            "Invitee_id" => $this->invitee->id,
            "FormRecord_id" => $this->formRecord->id,
            "id" => $this->id,
        ];
    }

}
