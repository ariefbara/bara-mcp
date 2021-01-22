<?php

namespace Tests\Controllers\RecordPreparation\Firm;

use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\RecordOfFirm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;

class RecordOfBioForm implements Record
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
    public $disabled;

    public function __construct(RecordOfFirm $firm, RecordOfForm $form)
    {
        $this->firm = $firm;
        $this->form = $form;
        $this->disabled = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Firm_id" => $this->firm->id,
            "id" => $this->form->id,
            "disabled" => $this->disabled,
        ];
    }

}
