<?php

namespace Tests\Controllers\RecordPreparation\User;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfRegistrant;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\RecordOfUser;

class RecordOfUserRegistrant implements Record
{

    /**
     *
     * @var RecordOfUser
     */
    public $user;

    /**
     *
     * @var RecordOfRegistrant
     */
    public $registrant;
    public $id;

    public function __construct(RecordOfUser $user, RecordOfRegistrant $registrant)
    {
        $this->user = $user;
        $this->registrant = $registrant;
        $this->id = $registrant->id;
    }

    public function toArrayForDbEntry()
    {
        return [
            'User_id' => $this->user->id,
            'Registrant_id' => $this->registrant->id,
            'id' => $this->id,
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $this->registrant->insert($connection);
        $connection->table('UserRegistrant')->insert($this->toArrayForDbEntry());
    }

}
