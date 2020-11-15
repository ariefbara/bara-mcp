<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant;

use Tests\Controllers\RecordPreparation\{
    Firm\Program\Activity\RecordOfInvitee,
    Firm\Program\RecordOfParticipant,
    Record
};

class RecordOfActivityInvitation implements Record
{

    /**
     *
     * @var RecordOfParticipant
     */
    public $participant;

    /**
     *
     * @var RecordOfInvitee
     */
    public $invitee;
    public $id;

    function __construct(RecordOfParticipant $participant, RecordOfInvitee $invitee)
    {
        $this->participant = $participant;
        $this->invitee = $invitee;
        $this->id = $this->invitee->id;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Participant_id" => $this->participant->id,
            "Invitee_id" => $this->invitee->id,
            "id" => $this->id,
        ];
    }

}
