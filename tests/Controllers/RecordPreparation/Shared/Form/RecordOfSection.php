<?php

namespace Tests\Controllers\RecordPreparation\Shared\Form;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;

class RecordOfSection implements Record
{

    /**
     * 
     * @var RecordOfForm
     */
    public $form;
    public $id;
    public $name;
    public $position;
    public $removed;

    public function __construct(RecordOfForm $form, $index)
    {
        $this->form = $form;
        $this->id = "section-$index-id";
        $this->name = "section $index name";
        $this->position = "section $index position";
        $this->removed = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            'Form_id' => $this->form->id,
            'id' => $this->id,
            'name' => $this->name,
            'position' => $this->position,
            'removed' => $this->removed,
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('Section')->insert($this->toArrayForDbEntry());
    }

}
