<?php

namespace Tests\Controllers\RecordPreparation\Shared;

use DateTimeImmutable;
use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfFormRecord implements Record
{
    /**
     *
     * @var RecordOfForm
     */
    public $form;
    public $id, $submitTime, $removed = false;
    
    function __construct(RecordOfForm $form, $index)
    {
        $this->form = $form;
        $this->id = "report-$index-id";
        $this->submitTime = (new DateTimeImmutable())->format('Y-m-d H:i:s');
    }

    
    public function toArrayForDbEntry()
    {
        return [
            "Form_id" => $this->form->id,
            "id" => $this->id,
            "submitTime" => $this->submitTime,
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('FormRecord')->insert($this->toArrayForDbEntry());
    }

}
