<?php

namespace Tests\Controllers\RecordPreparation\User;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\RecordOfParticipant,
    Record,
    RecordOfUser
};

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
            'id' => $this->program->id,
        ];
    }

}
