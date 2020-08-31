<?php

namespace Tests\Controllers\RecordPreparation\Firm\Client;

use Tests\Controllers\RecordPreparation\ {
    Firm\RecordOfClient,
    Record,
    Shared\RecordOfFileInfo
};

class RecordOfClientFileInfo implements Record
{

    /**
     *
     * @var RecordOfClient
     */
    public $client;

    /**
     *
     * @var RecordOfFileInfo
     */
    public $fileInfo;
    public $id, $removed = false;

    public function __construct(RecordOfClient $client, RecordOfFileInfo $fileInfo)
    {
        $this->client = $client;
        $this->fileInfo = $fileInfo;
        $this->id = $fileInfo->id;
        $this->removed = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            'Client_id' => $this->client->id,
            'FileInfo_id' => $this->fileInfo->id,
            'id' => $this->id,
            'removed' => $this->removed,
        ];
    }

}
