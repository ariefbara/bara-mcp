<?php

namespace Tests\Controllers\RecordPreparation\Firm;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\RecordOfFirm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;


class RecordOfWorksheetForm implements Record
{

    /**
     *
     * @var RecordOfFirm
     */
    public $firm;

    /**
     *
     * @var RecordOfForm
     */
    public $form;
    public $id, $removed = false;

    function __construct(?RecordOfFirm $firm, RecordOfForm $form)
    {
        $this->firm = $firm;
        $this->form = $form;
        $this->id = $form->id;
        $this->removed = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Firm_id" => isset($this->firm) ? $this->firm->id : null,
            "Form_id" => $this->form->id,
            "id" => $this->id,
            "removed" => $this->removed,
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('WorksheetForm')->insert($this->toArrayForDbEntry());
    }

}
