<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Coordinator;

use Tests\Controllers\RecordPreparation\{
    Firm\Program\Activity\RecordOfInvitee,
    Firm\Program\RecordOfCoordinator,
    Record
};

class RecordOfActivityInvitation implements Record
{

    /**
     *
     * @var RecordOfCoordinator
     */
    public $coordinator;

    /**
     *
     * @var RecordOfInvitee
     */
    public $invitee;
    public $id;

    function __construct(RecordOfCoordinator $coordinator, RecordOfInvitee $invitee)
    {
        $this->coordinator = $coordinator;
        $this->invitee = $invitee;
        $this->id = $this->invitee->id;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Coordinator_id" => $this->coordinator->id,
            "Invitee_id" => $this->invitee->id,
            "id" => $this->id,
        ];
    }

}
