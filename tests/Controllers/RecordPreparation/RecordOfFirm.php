<?php

namespace Tests\Controllers\RecordPreparation;

class RecordOfFirm implements Record
{
    public $id, $name, $identifier, $suspended = false;
    public $url, $mailSenderAddress, $mailSenderName;
    
    public function __construct($index, $identifier)
    {
        $this->id = "firm-$index-id";
        $this->name = "firm $index name";
        $this->identifier = $identifier;
        $this->url = "http://firm-$index-url.com";
        $this->mailSenderAddress = "noreply@firm-$index.com";
        $this->mailSenderName = "firm $index name";
    }
    
    public function toArrayForDbEntry()
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "identifier" => $this->identifier,
            "url" => $this->url,
            "mailSenderAddress" => $this->mailSenderAddress,
            "mailSenderName" => $this->mailSenderName,
            "suspended" => $this->suspended,
        ];
    }

}
