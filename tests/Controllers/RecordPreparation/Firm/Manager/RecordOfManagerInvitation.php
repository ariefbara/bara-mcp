<?php

namespace Tests\Controllers\RecordPreparation\Firm\Manager;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\Activity\RecordOfInvitation,
    Firm\RecordOfManager,
    Record
};

class RecordOfManagerInvitation implements Record
{
    /**
     *
     * @var RecordOfManager
     */
    public $manager;
    /**
     *
     * @var RecordOfInvitation
     */
    public $invitation;
    public $id;
    
    function __construct(RecordOfManager $manager, RecordOfInvitation $invitation)
    {
        $this->manager = $manager;
        $this->invitation = $invitation;
        $this->id = $invitation->id;
    }

    
    public function toArrayForDbEntry()
    {
        return [
            "Manager_id" => $this->manager->id,
            "Invitation_id" => $this->invitation->id,
            "id" => $this->id,
        ];
    }

}
