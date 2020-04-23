<?php

namespace Tests\Controllers\RecordPreparation\Firm;

use Tests\Controllers\RecordPreparation\{
    Record,
    RecordOfFirm
};

class RecordOfProgram implements Record
{

    /**
     *
     * @var RecordOfFirm
     */
    public $firm;
    public $id, $name, $description, $published = false, $removed = false;

    public function __construct(RecordOfFirm $firm, $index)
    {
        $this->firm = $firm;
        $this->id = "program-$index";
        $this->name = "program $index name";
        $this->description = "program $index description";
        $this->removed = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Firm_id" => $this->firm->id,
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "published" => $this->published,
            "removed" => $this->removed,
        ];
    }

}
