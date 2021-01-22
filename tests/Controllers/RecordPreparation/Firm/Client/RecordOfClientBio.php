<?php

namespace Tests\Controllers\RecordPreparation\Firm\Client;

use Tests\Controllers\RecordPreparation\Firm\RecordOfBioForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClientCVForm;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;

class RecordOfClientBio implements Record
{

    /**
     * 
     * @var RecordOfClient
     */
    public $client;

    /**
     * 
     * @var RecordOfBioForm
     */
    public $bioForm;

    /**
     * 
     * @var RecordOfFormRecord
     */
    public $formRecord;
    public $removed;

    public function __construct(RecordOfClient $client, RecordOfBioForm $bioForm,
            RecordOfFormRecord $formRecord)
    {
        $this->client = $client;
        $this->bioForm = $bioForm;
        $this->formRecord = $formRecord;
        $this->removed = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Client_id" => $this->client->id,
            "BioForm_id" => $this->bioForm->form->id,
            "id" => $this->formRecord->id,
            "removed" => $this->removed,
        ];
    }

}
