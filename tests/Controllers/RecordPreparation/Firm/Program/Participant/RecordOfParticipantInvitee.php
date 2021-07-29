<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\Activity\RecordOfInvitee;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfParticipantInvitee implements Record
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

    public function __construct(RecordOfParticipant $participant, RecordOfInvitee $invitee)
    {
        $this->participant = $participant;
        $this->invitee = $invitee;
    }

    public function toArrayForDbEntry()
    {
        return [
            'Participant_id' => $this->participant->id,
            'Invitee_id' => $this->invitee->id,
            'id' => $this->invitee->id,
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $this->invitee->insert($connection);
        $connection->table('ParticipantInvitee')->insert($this->toArrayForDbEntry());
    }

}
