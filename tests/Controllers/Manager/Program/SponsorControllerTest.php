<?php

namespace Tests\Controllers\Manager\Program;

use Tests\Controllers\Manager\ProgramTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfSponsor;
use Tests\Controllers\RecordPreparation\Firm\RecordOfFirmFileInfo;
use Tests\Controllers\RecordPreparation\RecordOfFirm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFileInfo;

class SponsorControllerTest extends ProgramTestCase
{
    protected $sponsorUri;
    protected $sponsorOne;
    protected $sponsorTwo;
    protected $firmFileInfoOne;
    protected $sponsorRequest;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table("Sponsor")->truncate();
        $this->connection->table("FileInfo")->truncate();
        $this->connection->table("FirmFileInfo")->truncate();
        
        $this->sponsorUri = $this->programUri . "/{$this->program->id}/sponsors";
        
        $firm = $this->program->firm;
        
        $this->sponsorOne = new RecordOfSponsor($this->program, "1");
        $this->sponsorTwo = new RecordOfSponsor($this->program, "2");
        
        $fileInfoOne = new RecordOfFileInfo("1");
        $this->firmFileInfoOne = new RecordOfFirmFileInfo($firm, $fileInfoOne);
        
        $this->sponsorRequest = [
            "name" => "new sponsor name",
            "website" => "new.sponsor.web.id",
            "firmFileInfoIdOfLogo" => $this->firmFileInfoOne->id,
        ];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Sponsor")->truncate();
        $this->connection->table("FileInfo")->truncate();
        $this->connection->table("FirmFileInfo")->truncate();
    }
    
    protected function add()
    {
        $this->firmFileInfoOne->insert($this->connection);
        $this->post($this->sponsorUri, $this->sponsorRequest, $this->manager->token);
    }
    public function test_add_201()
    {
        $this->add();
        $this->seeStatusCode(201);
        $response = [
            "disabled" => false,
            "name" => $this->sponsorRequest["name"],
            "website" => $this->sponsorRequest["website"],
        ];
        $this->seeJsonContains($response);
        
        $sponsorRecord = [
            "Program_id" => $this->program->id,
            "disabled" => false,
            "name" => $this->sponsorRequest["name"],
            "website" => $this->sponsorRequest["website"],
            "FirmFileInfo_idOfLogo" => $this->firmFileInfoOne->id,
        ];
        $this->seeInDatabase("Sponsor", $sponsorRecord);
    }
    public function test_add_emptyName_400()
    {
        $this->sponsorRequest["name"] = "";
        $this->add();
        $this->seeStatusCode(400);
    }
    public function test_add_invalidWebsiteFormat_400()
    {
        $this->sponsorRequest["website"] = "invalid website format";
        $this->add();
        $this->seeStatusCode(400);
    }
    public function test_add_firmFileInfoNotFound_404()
    {
        $this->sponsorRequest["firmFileInfoIdOfLogo"] = "non existing file";
        $this->add();
        $this->seeStatusCode(404);
    }
    public function test_add_firmFileInfoNotOwnedByFirm_403()
    {
        $otherFirm = new RecordOfFirm("other");
        $otherFirm->insert($this->connection);
        $this->firmFileInfoOne->firm = $otherFirm;
        
        $this->add();
        $this->seeStatusCode(403);
    }
    
    protected function update()
    {
        $this->firmFileInfoOne->insert($this->connection);
        $this->sponsorOne->insert($this->connection);
        
        $uri = $this->sponsorUri . "/{$this->sponsorOne->id}/update";
        $this->put($uri, $this->sponsorRequest, $this->manager->token);
    }
    public function test_update_200()
    {
        $this->update();
        $this->seeStatusCode(200);
        $response = [
            "id" => $this->sponsorOne->id,
            "disabled" => false,
            "name" => $this->sponsorRequest["name"],
            "website" => $this->sponsorRequest["website"],
        ];
        $this->seeJsonContains($response);
        
        $sponsorRecord = [
            "Program_id" => $this->program->id,
            "id" => $this->sponsorOne->id,
            "disabled" => false,
            "name" => $this->sponsorRequest["name"],
            "website" => $this->sponsorRequest["website"],
            "FirmFileInfo_idOfLogo" => $this->firmFileInfoOne->id,
        ];
        $this->seeInDatabase("Sponsor", $sponsorRecord);
    }
    public function test_update_emptyName_400()
    {
        $this->sponsorRequest["name"] = "";
        $this->update();
        $this->seeStatusCode(400);
    }
    public function test_update_invalidWebsiteFormat_400()
    {
        $this->sponsorRequest["website"] = "invalid website format";
        $this->update();
        $this->seeStatusCode(400);
    }
    public function test_update_firmFileInfoNotFound_404()
    {
        $this->sponsorRequest["firmFileInfoIdOfLogo"] = "non existing file";
        $this->update();
        $this->seeStatusCode(404);
    }
    public function test_update_firmFileInfoNotOwnedByFirm_403()
    {
        $otherFirm = new RecordOfFirm("other");
        $otherFirm->insert($this->connection);
        $this->firmFileInfoOne->firm = $otherFirm;
        
        $this->update();
        $this->seeStatusCode(403);
    }
    
    protected function disable()
    {
        $this->sponsorOne->insert($this->connection);
        $uri = $this->sponsorUri . "/{$this->sponsorOne->id}";
        $this->delete($uri, [], $this->manager->token);
    }
    public function test_disable_200()
    {
        $this->disable();
        $this->seeStatusCode(200);
        $response = [
            "id" => $this->sponsorOne->id,
            "disabled" => true,
        ];
        $this->seeJsonContains($response);
        
        $sponsorRecord = [
            "Program_id" => $this->program->id,
            "id" => $this->sponsorOne->id,
            "disabled" => true,
        ];
        $this->seeInDatabase("Sponsor", $sponsorRecord);
    }
    
    protected function enable()
    {
        $this->sponsorOne->disabled = true;
        $this->sponsorOne->insert($this->connection);
        $uri = $this->sponsorUri . "/{$this->sponsorOne->id}/enable";
        $this->put($uri, [], $this->manager->token);
    }
    public function test_enable_200()
    {
        $this->enable();
        $this->seeStatusCode(200);
        $response = [
            "id" => $this->sponsorOne->id,
            "disabled" => false,
        ];
        $this->seeJsonContains($response);
        
        $sponsorRecord = [
            "Program_id" => $this->program->id,
            "id" => $this->sponsorOne->id,
            "disabled" => false,
        ];
        $this->seeInDatabase("Sponsor", $sponsorRecord);
    }
    
    protected function show()
    {
        $this->firmFileInfoOne->insert($this->connection);
        $this->sponsorOne->logo = $this->firmFileInfoOne;
        $this->sponsorOne->insert($this->connection);
        $uri = $this->sponsorUri . "/{$this->sponsorOne->id}";
        $this->get($uri, $this->manager->token);
    }
    public function test_get_200()
    {
        $this->show();
        $this->seeStatusCode(200);
        
        $response = [
            "id" => $this->sponsorOne->id,
            "disabled" => $this->sponsorOne->disabled,
            "name" => $this->sponsorOne->name,
            "website" => $this->sponsorOne->website,
            "logo" => [
                "id" => $this->sponsorOne->logo->id,
                "url" => "/{$this->sponsorOne->logo->fileInfo->name}",
            ],
        ];
        $this->seeJsonContains($response);
    }
    
    protected function showAll()
    {
        $this->sponsorOne->insert($this->connection);
        $this->sponsorTwo->insert($this->connection);
        
        $this->get($this->sponsorUri, $this->manager->token);
    }
    public function test_showAll_200()
    {
        $this->showAll();
        $this->seeStatusCode(200);
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->sponsorOne->id,
                    "disabled" => $this->sponsorOne->disabled,
                    "name" => $this->sponsorOne->name,
                    "website" => $this->sponsorOne->website,
                ],
                [
                    "id" => $this->sponsorTwo->id,
                    "disabled" => $this->sponsorTwo->disabled,
                    "name" => $this->sponsorTwo->name,
                    "website" => $this->sponsorTwo->website,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_activeStatusFilterUsed()
    {
        $this->sponsorOne->disabled = true;
        $this->sponsorUri .= "?activeStatus=true";
        $this->showAll();
        $this->seeStatusCode(200);
        $response = [
            "total" => 1,
            "list" => [
                [
                    "id" => $this->sponsorTwo->id,
                    "disabled" => $this->sponsorTwo->disabled,
                    "name" => $this->sponsorTwo->name,
                    "website" => $this->sponsorTwo->website,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
}
