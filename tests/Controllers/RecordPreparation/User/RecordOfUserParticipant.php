<?php

namespace Tests\Controllers\RecordPreparation\User;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\RecordOfUser;

class RecordOfUserParticipant implements Record
{
    /**
     *
     * @var RecordOfUser
     */
    public $user;
    /**
     *
     * @var RecordOfParticipant
     */
    public $participant;
    public $id;
    
    public function __construct(RecordOfUser $user, RecordOfParticipant $participant)
    {
        $this->user = $user;
        $this->participant = $participant;
        $this->id = $participant->id;
    }

    
    public function toArrayForDbEntry()
    {
        return [
            'User_id' => $this->user->id,
            'Participant_id' => $this->participant->id,
            'id' => $this->id,
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $this->participant->insert($connection);
        $connection->table('UserParticipant')->insert($this->toArrayForDbEntry());
    }

}
