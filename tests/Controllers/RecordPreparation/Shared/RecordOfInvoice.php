<?php

namespace Tests\Controllers\RecordPreparation\Shared;

use DateTime;
use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfInvoice implements Record
{

    public $id;
    public $issuedTime;
    public $expiredTime;
    public $paymentLink;
    public $settled = false;

    public function __construct($index)
    {
        $this->id = "invoice-$index-id";
        $this->issuedTime = (new DateTime())->format('Y-m-d H:i:s');
        $this->expiredTime = (new DateTime('+7 days'))->format('Y-m-d H:i:s');
        $this->paymentLink = 'xendit payment link';
        $this->settled = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            'id' => $this->id,
            'issuedTime' => $this->issuedTime,
            'expiredTime' => $this->expiredTime,
            'paymentLink' => $this->paymentLink,
            'settled' => $this->settled,
        ];
    }

    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('Invoice')->insert($this->toArrayForDbEntry());
    }

}
