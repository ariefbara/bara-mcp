<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\RecordPreparation\Firm\RecordOfBioForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProfileForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;

class BioFormControllerTest extends ManagerTestCase
{
    protected $bioFormUri;
    protected $bioFormOne;
    protected $bioFormTwo_disable;
    protected $createInput = [
        "name" => 'new profile form name',
        "description" => 'new profile form description',
        "stringFields" => [],
        "integerFields" => [],
        "textAreaFields" => [],
        "singleSelectFields" => [],
        "multiSelectFields" => [],
        "attachmentFields" => [],
        "sections" => [],
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->bioFormUri = $this->managerUri . "/bio-forms";
        $this->connection->table("Form")->truncate();
        $this->connection->table("BioForm")->truncate();
        
        $formOne = new RecordOfForm(1);
        $formTwo = new RecordOfForm(2);
        $this->connection->table("Form")->insert($formOne->toArrayForDbEntry());
        $this->connection->table("Form")->insert($formTwo->toArrayForDbEntry());
        
        $this->bioFormOne = new RecordOfBioForm($this->firm, $formOne);
        $this->bioFormTwo_disable = new RecordOfBioForm($this->firm, $formTwo);
        $this->bioFormTwo_disable->disabled = true;
        $this->connection->table("BioForm")->insert($this->bioFormOne->toArrayForDbEntry());
        $this->connection->table("BioForm")->insert($this->bioFormTwo_disable->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Form")->truncate();
        $this->connection->table("BioForm")->truncate();
    }
    
    public function test_create_201()
    {
        $response = [
            "name" => $this->createInput["name"],
            "description" => $this->createInput["description"],
            "disabled" => false,
        ];
        
        $this->post($this->bioFormUri, $this->createInput, $this->manager->token)
                ->seeJsonContains($response)
                ->seeStatusCode(201);
        
        $bioFormEntry = [
            "disabled" => false,
        ];
        $this->seeInDatabase("BioForm", $bioFormEntry);
        $formEntry = [
            "name" => $this->createInput["name"],
            "description" => $this->createInput["description"],
        ];
        $this->seeInDatabase("Form", $formEntry);
    }
    
    public function test_update_200()
    {
        $uri = $this->bioFormUri . "/{$this->bioFormOne->form->id}/update";
        $this->patch($uri, $this->createInput, $this->manager->token)
                ->seeStatusCode(200);
        
        $response = [
            "id" => $this->bioFormOne->form->id,
            "name" => $this->createInput["name"],
            "description" => $this->createInput["description"],
            "disabled" => false,
        ];
        $this->seeJsonContains($response);
        
        $formEntry = [
            "id" => $this->bioFormOne->form->id,
            "name" => $this->createInput["name"],
            "description" => $this->createInput["description"],
        ];
        $this->seeInDatabase("Form", $formEntry);
    }

    public function test_disable_200()
    {
        $uri = $this->bioFormUri . "/{$this->bioFormOne->form->id}/disable";
        $this->patch($uri, [], $this->manager->token)
                ->seeStatusCode(200);
        
        $response = [
            "id" => $this->bioFormOne->form->id,
            "disabled" => true,
        ];
        $this->seeJsonContains($response);
        
        $bioFormEntry = [
            "id" => $this->bioFormOne->form->id,
            "disabled" => true,
        ];
        $this->seeInDatabase("BioForm", $bioFormEntry);
    }

    public function test_enable_200()
    {
        $uri = $this->bioFormUri . "/{$this->bioFormTwo_disable->form->id}/enable";
        $this->patch($uri, [], $this->manager->token)
                ->seeStatusCode(200);
        
        $response = [
            "id" => $this->bioFormTwo_disable->form->id,
            "disabled" => false,
        ];
        $this->seeJsonContains($response);
        
        $bioFormEntry = [
            "id" => $this->bioFormTwo_disable->form->id,
            "disabled" => false,
        ];
        $this->seeInDatabase("BioForm", $bioFormEntry);
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->bioFormOne->form->id,
            "disabled" => false,
            "name" => $this->bioFormOne->form->name,
            "description" => $this->bioFormOne->form->description,
            "stringFields" => [],
            "integerFields" => [],
            "textAreaFields" => [],
            "singleSelectFields" => [],
            "multiSelectFields" => [],
            "attachmentFields" => [],
            "sections" => [],
        ];
        $uri = $this->bioFormUri . "/{$this->bioFormOne->form->id}";
        $this->get($uri, $this->manager->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
        
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->bioFormOne->form->id,
                    "name" => $this->bioFormOne->form->name,
                    "disabled" => $this->bioFormOne->disabled,
                ],
                [
                    "id" => $this->bioFormTwo_disable->form->id,
                    "name" => $this->bioFormTwo_disable->form->name,
                    "disabled" => $this->bioFormTwo_disable->disabled,
                ],
            ],
        ];
        
        $this->get($this->bioFormUri, $this->manager->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_useQueryFilter_200()
    {
        $uri = $this->bioFormUri . "?disableStatus=false";
        $this->get($uri, $this->manager->token)
                ->seeStatusCode(200);
        
        $totalResponse = ["total" => 1];
        $this->seeJsonContains($totalResponse);
        
        $formOneResponse = [
            "id" => $this->bioFormOne->form->id,
        ];
        $this->seeJsonContains($formOneResponse);
    }
}
