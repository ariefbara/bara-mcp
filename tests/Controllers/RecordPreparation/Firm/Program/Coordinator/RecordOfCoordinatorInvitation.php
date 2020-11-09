<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Coordinator;

use Tests\Controllers\RecordPreparation\{
    Firm\Program\Activity\RecordOfInvitation,
    Firm\Program\RecordOfCoordinator,
    Record
};

class RecordOfCoordinatorInvitation implements Record
{

    /**
     *
     * @var RecordOfCoordinator
     */
    public $coordinator;

    /**
     *
     * @var RecordOfInvitation
     */
    public $invitation;
    public $id;

    function __construct(RecordOfCoordinator $coordinator, RecordOfInvitation $invitation)
    {
        $this->coordinator = $coordinator;
        $this->invitation = $invitation;
        $this->id = $invitation->id;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Coordinator_id" => $this->coordinator->id,
            "Invitation_id" => $this->invitation->id,
            "id" => $this->id,
        ];
    }

}
