<?php

namespace Tests\Controllers\RecordPreparation\Firm;

use Tests\Controllers\RecordPreparation\ {
    Record,
    RecordOfFirm,
    Shared\RecordOfForm
};


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

    function __construct(RecordOfFirm $firm, RecordOfForm $form)
    {
        $this->firm = $firm;
        $this->form = $form;
        $this->id = $form->id;
        $this->removed = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Firm_id" => $this->firm->id,
            "Form_id" => $this->form->id,
            "id" => $this->id,
            "removed" => $this->removed,
        ];
    }

}
