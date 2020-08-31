<?php

namespace Tests\Controllers\RecordPreparation\User;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\RecordOfRegistrant,
    Record,
    RecordOfUser
};

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

}
