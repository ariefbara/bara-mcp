<?php

namespace Tests\Controllers\Manager;

use DateTimeImmutable;
use Tests\Controllers\RecordPreparation\Firm\RecordOfBioForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfFirmFileInfo;
use Tests\Controllers\RecordPreparation\RecordOfFirm;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfIntegerField;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFileInfo;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;

class FirmControllerTest extends ManagerTestCase
{
    protected $firmUri;
    protected $firmFileInfoLogo;
    protected $updateProfileInput;
    protected $bioFormOne;
    protected $integerField_11;
    protected $bioSearchFilterRequest;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->firmUri = $this->managerUri . "/firm-profile";
        
        $this->connection->table("FileInfo")->truncate();
        $this->connection->table("FirmFileInfo")->truncate();
        $this->connection->table('BioSearchFilter')->truncate();
        $this->connection->table('IntegerFieldSearchFilter')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('BioForm')->truncate();
        $this->connection->table('IntegerField')->truncate();
        
        $firm = $this->manager->firm;
        
        $fileInfo = new RecordOfFileInfo(0);
        $this->connection->table("FileInfo")->insert($fileInfo->toArrayForDbEntry());
        
        $this->firmFileInfoLogo = new RecordOfFirmFileInfo($this->firm, $fileInfo);
        $this->connection->table("FirmFileInfo")->insert($this->firmFileInfoLogo->toArrayForDbEntry());
        
        $formOne = new RecordOfForm('1');
        
        $this->bioFormOne = new RecordOfBioForm($firm, $formOne);
        
        $this->integerField_11 = new RecordOfIntegerField($formOne, '11');
        
        $this->bioSearchFilterRequest = [
            'bioForms' => [
                [
                    'id' => $this->bioFormOne->form->id,
                    'integerFields' => [
                        [
                            'id' => $this->integerField_11->id,
                            'comparisonType' => 2,
                        ],
                    ],
                    'stringFields' => [],
                    'textAreaFields' => [],
                    'singleSelectFields' => [],
                    'multiSelectFields' => [],
                ],
            ],
        ];
        
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
        $this->connection->table('BioSearchFilter')->truncate();
        $this->connection->table('IntegerFieldSearchFilter')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('BioForm')->truncate();
        $this->connection->table('IntegerField')->truncate();
    }
    
    public function test_update_200()
    {
        $response = [
            "name" => $this->firm->name,
            "domain" => $this->firm->url,
            "mailSenderAddress" => $this->firm->mailSenderAddress,
            "mailSenderName" => $this->firm->mailSenderName,
            "logo" => [
                "id" => $this->firmFileInfoLogo->id,
                "path" => DIRECTORY_SEPARATOR . $this->firmFileInfoLogo->fileInfo->name,
            ],
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
    
    protected function executeSetBioSearchFilter()
    {
        $this->bioFormOne->insert($this->connection);
        $this->integerField_11->insert($this->connection);
        
        $this->firmUri .= "/bio-search-filter";
        $this->put($this->firmUri, $this->bioSearchFilterRequest, $this->manager->token);
    }
    public function test_setBioSearch_200()
    {
        $this->executeSetBioSearchFilter();
        $this->seeStatusCode(200);
        
        $bioSearchFilterResponse = [
            'disabled' => false,
            'modifiedTime' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
        ];
        $this->seeJsonContains($bioSearchFilterResponse);
        
        $integerFieldSearchFilterResponse = [
            'integerField' => [
                'id' => $this->integerField_11->id,
                'name' => $this->integerField_11->name,
            ],
            'disabled' => false,
            'comparisonType' => 2,
            'comparisonTypeDisplayValue' => 'LESS_THAN',
        ];
        $this->seeJsonContains($integerFieldSearchFilterResponse);
        
        $bioSearchFilterEntry = [
            'Firm_id' => $this->manager->firm->id,
            'disabled' => false,
            'modifiedTime' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
        ];
        $this->seeInDatabase('BioSearchFilter', $bioSearchFilterEntry);
        
        $integerFieldSearchFilterEntry = [
            'IntegerField_id' => $this->integerField_11->id,
            'disabled' => false,
            'comparisonType' => 2,
        ];
        $this->seeInDatabase('IntegerFieldSearchFilter', $integerFieldSearchFilterEntry);
    }
    public function test_setBioSearch_unmanagedBioForm_403()
    {
        $this->bioFormOne->firm = new RecordOfFirm('other');
        $this->bioFormOne->firm->insert($this->connection);
        
        $this->executeSetBioSearchFilter();
        $this->seeStatusCode(403);
    }
    public function test_setBioSearch_removedManager_401()
    {
        $this->bioFormOne->insert($this->connection);
        $this->integerField_11->insert($this->connection);
        
        $this->firmUri .= "/bio-search-filter";
        $this->put($this->firmUri, $this->bioSearchFilterRequest, $this->removedManager->token);
        $this->seeStatusCode(401);
    }
    
    public function test_show_200()
    {
        $response = [
            "name" => $this->firm->name,
            "domain" => $this->firm->url,
            "mailSenderAddress" => $this->firm->mailSenderAddress,
            "mailSenderName" => $this->firm->mailSenderName,
            "logo" => null,
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
