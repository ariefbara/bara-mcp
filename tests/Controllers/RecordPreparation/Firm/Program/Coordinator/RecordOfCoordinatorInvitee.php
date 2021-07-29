<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Coordinator;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\Activity\RecordOfInvitee;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfCoordinator;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfCoordinatorInvitee implements Record
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

    public function __construct(RecordOfCoordinator $coordinator, RecordOfInvitee $invitee)
    {
        $this->coordinator = $coordinator;
        $this->invitee = $invitee;
    }

    public function toArrayForDbEntry()
    {
        return [
            'Coordinator_id' => $this->coordinator->id,
            'Invitee_id' => $this->invitee->id,
            'id' => $this->invitee->id,
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $this->invitee->insert($connection);
        $connection->table('CoordinatorInvitee')->insert($this->toArrayForDbEntry());
    }

}
