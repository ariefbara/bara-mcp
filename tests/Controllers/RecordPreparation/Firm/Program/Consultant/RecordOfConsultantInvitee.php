<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Consultant;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\Activity\RecordOfInvitee;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfConsultantInvitee implements Record
{

    /**
     * 
     * @var RecordOfConsultant
     */
    public $consultant;

    /**
     * 
     * @var RecordOfInvitee
     */
    public $invitee;

    public function __construct(RecordOfConsultant $consultant, RecordOfInvitee $invitee)
    {
        $this->consultant = $consultant;
        $this->invitee = $invitee;
    }

    public function toArrayForDbEntry()
    {
        return [
            'Consultant_id' => $this->consultant->id,
            'Invitee_id' => $this->invitee->id,
            'id' => $this->invitee->id,
        ];
    }

    public function insert(ConnectionInterface $connection): void
    {
        $this->invitee->insert($connection);
        $connection->table('ConsultantInvitee')->insert($this->toArrayForDbEntry());
    }

}
