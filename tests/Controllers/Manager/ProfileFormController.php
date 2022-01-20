<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\RecordPreparation\Firm\RecordOfProfileForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;

class ProfileFormController extends ManagerTestCase
{
    protected $profileFormUri;
    protected $profileFormOne;
    protected $profileFormTwo;
    protected $profileFormInput = [
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
        
        $this->profileFormUri = $this->managerUri . "/profile-forms";
        $this->connection->table("Form")->truncate();
        $this->connection->table("ProfileForm")->truncate();
        
        $formOne = new RecordOfForm(1);
        $formTwo = new RecordOfForm(2);
        $this->connection->table("Form")->insert($formOne->toArrayForDbEntry());
        $this->connection->table("Form")->insert($formTwo->toArrayForDbEntry());
        
        $this->profileFormOne = new RecordOfProfileForm($this->firm, $formOne);
        $this->profileFormTwo = new RecordOfProfileForm($this->firm, $formTwo);
        $this->connection->table("ProfileForm")->insert($this->profileFormOne->toArrayForDbEntry());
        $this->connection->table("ProfileForm")->insert($this->profileFormTwo->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Form")->truncate();
        $this->connection->table("ProfileForm")->truncate();
    }
    
    public function test_create_201()
    {
        $this->connection->table("ProfileForm")->truncate();
        $response = [
            "name" => $this->profileFormInput["name"],
            "description" => $this->profileFormInput["description"],
        ];
        $this->post($this->profileFormUri, $this->profileFormInput, $this->manager->token)
                ->seeJsonContains($response)
                ->seeStatusCode(201);
        
        $formEntry = [
            "name" => $this->profileFormInput["name"],
            "description" => $this->profileFormInput["description"],
        ];
        $this->seeInDatabase("Form", $formEntry);
        
        $profileFormEntry = [
            "Firm_id" => $this->firm->id,
        ];
        $this->seeInDatabase("ProfileForm", $profileFormEntry);
        
    }
    
    public function test_update_200()
    {
        $response = [
            "id" => $this->profileFormOne->id,
            "name" => $this->profileFormInput["name"],
            "description" => $this->profileFormInput["description"],
        ];
        
        $uri = $this->profileFormUri . "/{$this->profileFormOne->id}";
        $this->patch($uri, $this->profileFormInput, $this->manager->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
        
        $formEntry = [
            "id" => $this->profileFormOne->form->id,
            "name" => $this->profileFormInput["name"],
            "description" => $this->profileFormInput["description"],
        ];
        $this->seeInDatabase("Form", $formEntry);
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->profileFormOne->id,
            "name" => $this->profileFormOne->form->name,
            "description" => $this->profileFormOne->form->description,
            "stringFields" => [],
            "integerFields" => [],
            "textAreaFields" => [],
            "attachmentFields" => [],
            "singleSelectFields" => [],
            "multiSelectFields" => [],
            "sections" => [],
        ];
        
        $uri = $this->profileFormUri . "/{$this->profileFormOne->id}";
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
                    "id" => $this->profileFormOne->id,
                    "name" => $this->profileFormOne->form->name,
                ],
                [
                    "id" => $this->profileFormTwo->id,
                    "name" => $this->profileFormTwo->form->name,
                ],
            ],
        ];
        $this->get($this->profileFormUri, $this->manager->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
}
