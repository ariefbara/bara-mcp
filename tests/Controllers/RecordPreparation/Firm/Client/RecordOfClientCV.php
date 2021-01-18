<?php

namespace Tests\Controllers\RecordPreparation\Firm\Client;

use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClientCVForm;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;

class RecordOfClientCV implements Record
{

    /**
     * 
     * @var RecordOfClient
     */
    public $client;

    /**
     * 
     * @var RecordOfClientCVForm
     */
    public $clientCVForm;

    /**
     * 
     * @var RecordOfFormRecord
     */
    public $formRecord;
    public $id;
    public $removed;

    public function __construct(RecordOfClient $client, RecordOfClientCVForm $clientCVForm,
            RecordOfFormRecord $formRecord)
    {
        $this->client = $client;
        $this->clientCVForm = $clientCVForm;
        $this->formRecord = $formRecord;
        $this->id = $formRecord->id;
        $this->removed = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Client_id" => $this->client->id,
            "ClientCVForm_id" => $this->clientCVForm->id,
            "FormRecord_id" => $this->formRecord->id,
            "id" => $this->id,
            "removed" => $this->removed,
        ];
    }

}
