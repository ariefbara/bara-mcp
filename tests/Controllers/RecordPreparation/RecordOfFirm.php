<?php

namespace Tests\Controllers\RecordPreparation;

use Tests\Controllers\RecordPreparation\Firm\RecordOfFirmFileInfo;

class RecordOfFirm implements Record
{
    public $id, $name, $identifier, $suspended = false;
    public $url, $mailSenderAddress, $mailSenderName;
    /**
     *
     * @var RecordOfFirmFileInfo
     */
    public $logo;
    public $displaySetting;
    
    public function __construct($index)
    {
        $this->id = "firm-$index-id";
        $this->name = "firm $index name";
        $this->identifier = "firm-$index-identifier";
        $this->url = "http://firm-$index-url.com";
        $this->mailSenderAddress = "noreply@firm-$index.com";
        $this->mailSenderName = "firm $index name";
        $this->logo = null;
        $this->displaySetting = null;
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
            "FirmFileInfo_idOfLogo" => empty($this->logo)? null: $this->logo->id,
            "displaySetting" => $this->displaySetting,
        ];
    }

}
