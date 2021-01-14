<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\RecordPreparation\Firm\RecordOfClientCVForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProfileForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;

class ClientCVFormControllerTest extends ManagerTestCase
{
    protected $clientCVFormUri;
    protected $clientCVFormOne;
    protected $clientCVFormTwo_disable;
    protected $profileForm;
    protected $assignInput;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientCVFormUri = $this->managerUri . "/client-cv-forms";
        $this->connection->table("Form")->truncate();
        $this->connection->table("ProfileForm")->truncate();
        $this->connection->table("ClientCVForm")->truncate();
        
        $form = new RecordOfForm(0);
        $formOne = new RecordOfForm(1);
        $formTwo = new RecordOfForm(2);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());
        $this->connection->table("Form")->insert($formOne->toArrayForDbEntry());
        $this->connection->table("Form")->insert($formTwo->toArrayForDbEntry());
        
        $this->profileForm = new RecordOfProfileForm($this->firm, $form);
        $profileFormOne = new RecordOfProfileForm($this->firm, $formOne);
        $profileFormTwo = new RecordOfProfileForm($this->firm, $formTwo);
        $this->connection->table("ProfileForm")->insert($this->profileForm->toArrayForDbEntry());
        $this->connection->table("ProfileForm")->insert($profileFormOne->toArrayForDbEntry());
        $this->connection->table("ProfileForm")->insert($profileFormTwo->toArrayForDbEntry());
        
        $this->clientCVFormOne = new RecordOfClientCVForm($this->firm, $profileFormOne, 1);
        $this->clientCVFormTwo_disable = new RecordOfClientCVForm($this->firm, $profileFormTwo, 2);
        $this->clientCVFormTwo_disable->disabled = true;
        $this->connection->table("ClientCVForm")->insert($this->clientCVFormOne->toArrayForDbEntry());
        $this->connection->table("ClientCVForm")->insert($this->clientCVFormTwo_disable->toArrayForDbEntry());
        
        $this->assignInput = [
            "profileFormId" => $this->profileForm->id,
        ];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Form")->truncate();
        $this->connection->table("ProfileForm")->truncate();
        $this->connection->table("ClientCVForm")->truncate();
    }
    
    public function test_assign_200()
    {
        $profileFormResponse = [
            "id" => $this->profileForm->id,
            "name" => $this->profileForm->form->name,
            "disabled" => false,
        ];
        
        $this->post($this->clientCVFormUri, $this->assignInput, $this->manager->token)
                ->seeJsonContains($profileFormResponse)
                ->seeStatusCode(200);
        
        $clientCVFormEntry = [
            "Firm_id" => $this->firm->id,
            "ProfileForm_id" => $this->profileForm->id,
            "disabled" => false,
        ];
        $this->seeInDatabase("ClientCVForm", $clientCVFormEntry);
    }
    
    public function test_disable_200()
    {
        $profileFormResponse = [
            "id" => $this->clientCVFormOne->id,
            "disabled" => true,
        ];
        
        $uri = $this->clientCVFormUri . "/{$this->clientCVFormOne->id}";
        $this->delete($uri, [], $this->manager->token)
                ->seeJsonContains($profileFormResponse)
                ->seeStatusCode(200);
        
        $clientCVFormEntry = [
            "id" => $this->clientCVFormOne->id,
            "disabled" => true,
        ];
        $this->seeInDatabase("ClientCVForm", $clientCVFormEntry);
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->clientCVFormOne->id,
            "disabled" => false,
            "profileForm" => [
                "id" => $this->clientCVFormOne->profileForm->id,
                "name" => $this->clientCVFormOne->profileForm->form->name,
                "description" => $this->clientCVFormOne->profileForm->form->description,
                "stringFields" => [],
                "integerFields" => [],
                "textAreaFields" => [],
                "singleSelectFields" => [],
                "multiSelectFields" => [],
                "attachmentFields" => [],
                
            ],
        ];
        $uri = $this->clientCVFormUri . "/{$this->clientCVFormOne->id}";
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
                    "id" => $this->clientCVFormOne->id,
                    "disabled" => $this->clientCVFormOne->disabled,
                    "profileForm" => [
                        "id" => $this->clientCVFormOne->profileForm->id,
                        "name" => $this->clientCVFormOne->profileForm->form->name,
                    ],
                ],
                [
                    "id" => $this->clientCVFormTwo_disable->id,
                    "disabled" => $this->clientCVFormTwo_disable->disabled,
                    "profileForm" => [
                        "id" => $this->clientCVFormTwo_disable->profileForm->id,
                        "name" => $this->clientCVFormTwo_disable->profileForm->form->name,
                    ],
                ],
            ],
        ];
        
        $this->get($this->clientCVFormUri, $this->manager->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
}
