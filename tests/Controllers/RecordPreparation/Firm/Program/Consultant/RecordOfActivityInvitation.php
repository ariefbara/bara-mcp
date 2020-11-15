<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Consultant;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\Activity\RecordOfInvitee,
    Firm\Program\RecordOfConsultant,
    Record
};

class RecordOfActivityInvitation implements Record
{
    
    /**
     *
     * @var RecordOfConsultant
     */
    public $consultant;

    /**
     *
     * @var RecordOfInvitee
     */
    public $invitee;
    public $id;

    function __construct(RecordOfConsultant $consultant, RecordOfInvitee $invitee)
    {
        $this->consultant = $consultant;
        $this->invitee = $invitee;
        $this->id = $this->invitee->id;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Consultant_id" => $this->consultant->id,
            "Invitee_id" => $this->invitee->id,
            "id" => $this->id,
        ];
    }
    
}
