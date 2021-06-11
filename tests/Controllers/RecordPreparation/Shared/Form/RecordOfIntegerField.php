<?php

namespace Tests\Controllers\RecordPreparation\Shared\Form;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;

class RecordOfIntegerField implements Record
{
    /**
     *
     * @var RecordOfForm
     */
    public $form;
    public $id, $name, $description, $position, $mandatory = false, 
            $minValue = null, $maxValue = null, $placeholder, $defaultValue = null, $removed = false;
    
    function __construct(RecordOfForm $form, $index)
    {
        $this->form = $form;
        $this->id = "integer-field-$index-id";
        $this->name = "integer field $index name";
        $this->description= "integer field $index description";
        $this->position = "integer field $index position";
        $this->mandatory = false;
        $this->minValue = null;
        $this->maxValue = null;
        $this->placeholder = "integer field $index placeholder";
        $this->defaultValue = null;
        $this->removed = false;
    }

    
    public function toArrayForDbEntry()
    {
        return [
            "Form_id" => $this->form->id,
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "position" => $this->position,
            "mandatory" => $this->mandatory,
            "minimumValue" => $this->minValue,
            "maximumValue" => $this->maxValue,
            "placeholder" => $this->placeholder,
            "defaultValue" => $this->defaultValue,
            "removed" => $this->removed,
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('IntegerField')->insert($this->toArrayForDbEntry());
    }
}
