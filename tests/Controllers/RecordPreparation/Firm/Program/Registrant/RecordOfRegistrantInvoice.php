<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Registrant;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfRegistrant;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\Shared\RecordOfInvoice;

class RecordOfRegistrantInvoice implements Record
{

    /**
     * 
     * @var RecordOfRegistrant
     */
    public $registrant;

    /**
     * 
     * @var RecordOfInvoice
     */
    public $invoice;

    public function __construct(RecordOfRegistrant $registrant, RecordOfInvoice $invoice)
    {
        $this->registrant = $registrant;
        $this->invoice = $invoice;
    }

    public function toArrayForDbEntry()
    {
        return [
            'Registrant_id' => $this->registrant->id,
            'id' => $this->invoice->id,
            'Invoice_id' => $this->invoice->id,
        ];
    }

    public function insert(ConnectionInterface $connection): void
    {
        $this->invoice->insert($connection);
        $connection->table('RegistrantInvoice')->insert($this->toArrayForDbEntry());
    }

}
