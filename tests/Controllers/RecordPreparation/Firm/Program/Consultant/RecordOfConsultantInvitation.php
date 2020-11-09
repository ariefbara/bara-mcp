<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Consultant;

use Tests\Controllers\RecordPreparation\{
    Firm\Program\Activity\RecordOfInvitation,
    Firm\Program\RecordOfConsultant,
    Record
};

class RecordOfConsultantInvitation implements Record
{

    /**
     *
     * @var RecordOfConsultant
     */
    public $consultant;

    /**
     *
     * @var RecordOfInvitation
     */
    public $invitation;
    public $id;

    function __construct(RecordOfConsultant $consultant, RecordOfInvitation $invitation)
    {
        $this->consultant = $consultant;
        $this->invitation = $invitation;
        $this->id = $invitation->id;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Consultant_id" => $this->consultant->id,
            "Invitation_id" => $this->invitation->id,
            "id" => $this->id,
        ];
    }

}
