<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\Shared\RecordOfInvoice;

class RecordOfParticipantInvoice implements Record
{
    /**
     * 
     * @var RecordOfParticipant
     */
    public $participant;
    /**
     * 
     * @var RecordOfInvoice
     */
    public $invoice;
    public $id;
    
    public function __construct(RecordOfParticipant $participant, RecordOfInvoice $invoice)
    {
        $this->participant = $participant;
        $this->invoice = $invoice;
        $this->id = $this->invoice->id;
    }

    
    public function toArrayForDbEntry()
    {
        return [
            'Participant_id' => $this->participant->id,
            'id' => $this->id,
            'Invoice_id' => $this->invoice->id,
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $this->invoice->insert($connection);
        $connection->table('ParticipantInvoice')->insert($this->toArrayForDbEntry());
    }

}
