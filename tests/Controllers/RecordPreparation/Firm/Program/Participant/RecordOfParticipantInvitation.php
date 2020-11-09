<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant;

use Tests\Controllers\RecordPreparation\{
    Firm\Program\Activity\RecordOfInvitation,
    Firm\Program\RecordOfParticipant,
    Record
};

class RecordOfParticipantInvitation implements Record
{

    /**
     *
     * @var RecordOfParticipant
     */
    public $participant;

    /**
     *
     * @var RecordOfInvitation
     */
    public $invitation;
    public $id;

    function __construct(RecordOfParticipant $participant, RecordOfInvitation $invitation)
    {
        $this->participant = $participant;
        $this->invitation = $invitation;
        $this->id = $invitation->id;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Participant_id" => $this->participant->id,
            "Invitation_id" => $this->invitation->id,
            "id" => $this->id,
        ];
    }

}
