<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\RecordPreparation\ {
    Firm\RecordOfFirmFileInfo,
    Shared\RecordOfFileInfo
};

class FirmControllerTest extends ManagerTestCase
{
    protected $firmUri;
    protected $firmFileInfoLogo;
    protected $updateProfileInput;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->firmUri = $this->managerUri . "/firm-profile";
        
        $this->connection->table("FileInfo")->truncate();
        $this->connection->table("FirmFileInfo")->truncate();
        
        $fileInfo = new RecordOfFileInfo(0);
        $this->connection->table("FileInfo")->insert($fileInfo->toArrayForDbEntry());
        
        
        $this->firmFileInfoLogo = new RecordOfFirmFileInfo($this->firm, $fileInfo);
        $this->connection->table("FirmFileInfo")->insert($this->firmFileInfoLogo->toArrayForDbEntry());
        
        $this->updateProfileInput = [
            "firmFileInfoIdOfLogo" => $this->firmFileInfoLogo->id,
            "displaySetting" => "{setting: 'new display setting'}",
        ];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("FileInfo")->truncate();
        $this->connection->table("FirmFileInfo")->truncate();
    }
    
    public function test_update_200()
    {
        $response = [
            "name" => $this->firm->name,
            "domain" => $this->firm->url,
            "mailSenderAddress" => $this->firm->mailSenderAddress,
            "mailSenderName" => $this->firm->mailSenderName,
            "logoPath" => DIRECTORY_SEPARATOR . $this->firmFileInfoLogo->fileInfo->name,
            "displaySetting" => $this->updateProfileInput["displaySetting"],
        ];
        
        $uri = $this->firmUri . "/update";
        $this->patch($uri, $this->updateProfileInput, $this->manager->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_update_inactiveManager_401()
    {
        $uri = $this->firmUri . "/update";
        $this->patch($uri, $this->updateProfileInput, $this->removedManager->token)
                ->seeStatusCode(401);
    }
    
    public function test_show_200()
    {
        $response = [
            "name" => $this->firm->name,
            "domain" => $this->firm->url,
            "mailSenderAddress" => $this->firm->mailSenderAddress,
            "mailSenderName" => $this->firm->mailSenderName,
            "logoPath" => null,
            "displaySetting" => $this->firm->displaySetting,
        ];
        $this->get($this->firmUri, $this->manager->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_show_inactiveManager_403()
    {
        $this->get($this->firmUri, $this->removedManager->token)
                ->seeStatusCode(401);
    }
}
