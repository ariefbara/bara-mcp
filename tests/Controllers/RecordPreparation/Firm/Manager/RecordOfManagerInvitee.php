<?php

namespace Tests\Controllers\RecordPreparation\Firm\Manager;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\Activity\RecordOfInvitee;
use Tests\Controllers\RecordPreparation\Firm\RecordOfManager;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfManagerInvitee implements Record
{

    /**
     * 
     * @var RecordOfManager
     */
    public $manager;

    /**
     * 
     * @var RecordOfInvitee
     */
    public $invitee;

    public function __construct(RecordOfManager $manager, RecordOfInvitee $invitee)
    {
        $this->manager = $manager;
        $this->invitee = $invitee;
    }

    public function toArrayForDbEntry()
    {
        return [
            'Manager_id' => $this->manager->id,
            'Invitee_id' => $this->invitee->id,
            'id' => $this->invitee->id,
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $this->invitee->insert($connection);
        $connection->table('ManagerInvitee')->insert($this->toArrayForDbEntry());
    }

}
