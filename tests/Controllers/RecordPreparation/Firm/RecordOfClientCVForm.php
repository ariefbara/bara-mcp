<?php

namespace Tests\Controllers\RecordPreparation\Firm;

use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\RecordOfFirm;

class RecordOfClientCVForm implements Record
{

    /**
     * 
     * @var RecordOfFirm
     */
    public $firm;

    /**
     * 
     * @var RecordOfProfileForm
     */
    public $profileForm;
    public $id;
    public $disabled;

    public function __construct(RecordOfFirm $firm, RecordOfProfileForm $profileForm, $index)
    {
        $this->firm = $firm;
        $this->profileForm = $profileForm;
        $this->id = "clientCVForm-$index-id";
        $this->disabled = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Firm_id" => $this->firm->id,
            "ProfileForm_id" => $this->profileForm->id,
            "id" => $this->id,
            "disabled" => $this->disabled,
        ];
    }

}
