<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Coordinator;

use Illuminate\Database\Connection;
use Tests\Controllers\RecordPreparation\Firm\Program\Activity\RecordOfInvitee;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfCoordinator;
use Tests\Controllers\RecordPreparation\Record;

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

    function __construct(?RecordOfCoordinator $coordinator, ?RecordOfInvitee $invitee, $index = 1)
    {
        $this->coordinator = isset($coordinator)? $coordinator: new RecordOfCoordinator(null, null, $index);
        $this->invitee = isset($invitee)? $invitee: new RecordOfInvitee(null, null, $index);
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
    
    public function persistSelf(Connection $connection): void
    {
        $connection->table("CoordinatorInvitee")->insert($this->toArrayForDbEntry());
    }

}
