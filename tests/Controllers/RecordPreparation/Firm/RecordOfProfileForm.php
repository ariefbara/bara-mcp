<?php

namespace Tests\Controllers\RecordPreparation\Firm;

use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\RecordOfFirm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;

class RecordOfProfileForm implements Record
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
    public $id;

    function __construct(RecordOfFirm $firm, RecordOfForm $form)
    {
        $this->firm = $firm;
        $this->form = $form;
        $this->id = $form->id;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Firm_id" => $this->firm->id,
            "Form_id" => $this->form->id,
            "id" => $this->id,
        ];
    }

}
