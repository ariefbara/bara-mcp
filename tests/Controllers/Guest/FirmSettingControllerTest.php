<?php

namespace Tests\Controllers\Guest;

use Tests\Controllers\ {
    ControllerTestCase,
    RecordPreparation\Firm\RecordOfFirmFileInfo,
    RecordPreparation\RecordOfFirm,
    RecordPreparation\Shared\RecordOfFileInfo
};

class FirmSettingControllerTest extends ControllerTestCase
{
    protected $firmSettingUri;
    protected $firm;
    protected $firmFileInfo;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->firmSettingUri = "/api/guest/firm-setting";
        
        $this->connection->table("Firm")->truncate();
        $this->connection->table("FileInfo")->truncate();
        $this->connection->table("FirmFileInfo")->truncate();
        
        $this->firm = new RecordOfFirm(0);
        
        $fileInfo = new RecordOfFileInfo(0);
        $this->connection->table("FileInfo")->insert($fileInfo->toArrayForDbEntry());
        
        $this->firmFileInfo = new RecordOfFirmFileInfo($this->firm, $fileInfo);
        $this->connection->table("FirmFileInfo")->insert($this->firmFileInfo->toArrayForDbEntry());
        
        $this->firm->logo = $this->firmFileInfo;
        $this->connection->table("Firm")->insert($this->firm->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Firm")->truncate();
        $this->connection->table("FileInfo")->truncate();
        $this->connection->table("FirmFileInfo")->truncate();
    }
    
    public function test_show_200()
    {
        $response = [
            "logoPath" => DIRECTORY_SEPARATOR . $this->firmFileInfo->fileInfo->name,
            "displaySetting" => $this->firm->displaySetting,
        ];
        
        $uri = $this->firmSettingUri . "/{$this->firm->identifier}";
        $this->get($uri)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
}
