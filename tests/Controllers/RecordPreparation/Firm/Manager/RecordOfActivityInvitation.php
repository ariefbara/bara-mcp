<?php

namespace Tests\Controllers\RecordPreparation\Firm\Manager;

use Tests\Controllers\RecordPreparation\{
    Firm\Program\Activity\RecordOfInvitee,
    Firm\RecordOfManager,
    Record
};

class RecordOfActivityInvitation implements Record
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
    public $id;

    function __construct(RecordOfManager $manager, RecordOfInvitee $invitee)
    {
        $this->manager = $manager;
        $this->invitee = $invitee;
        $this->id = $this->invitee->id;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Manager_id" => $this->manager->id,
            "Invitee_id" => $this->invitee->id,
            "id" => $this->id,
        ];
    }

}
