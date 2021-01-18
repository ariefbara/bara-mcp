<?php

namespace Tests\Controllers\Client;

use Tests\Controllers\RecordPreparation\Firm\RecordOfClientCVForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProfileForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;

class ClientCVFormControllerTest extends ClientTestCase
{
    protected $clientCVFormUri;
    protected $clientCVFormOne;
    protected $clientCVFormTwo_disabled;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->clientCVFormUri = $this->clientUri . "/client-cv-forms";
        
        $this->connection->table("Form")->truncate();
        $this->connection->table("ProfileForm")->truncate();
        $this->connection->table("ClientCVForm")->truncate();
        
        $firm = $this->client->firm;
        
        $form = new RecordOfForm(0);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());
        
        $profileForm = new RecordOfProfileForm($firm, $form);
        $this->connection->table("ProfileForm")->insert($profileForm->toArrayForDbEntry());
        
        $this->clientCVFormOne = new RecordOfClientCVForm($firm, $profileForm, 1);
        $this->clientCVFormTwo_disabled = new RecordOfClientCVForm($firm, $profileForm, 2);
        $this->clientCVFormTwo_disabled->disabled = true;
        $this->connection->table("ClientCVForm")->insert($this->clientCVFormOne->toArrayForDbEntry());
        $this->connection->table("ClientCVForm")->insert($this->clientCVFormTwo_disabled->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Form")->truncate();
        $this->connection->table("ProfileForm")->truncate();
        $this->connection->table("ClientCVForm")->truncate();
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->clientCVFormOne->id,
            "disabled" => $this->clientCVFormOne->disabled,
            "profileForm" => [
                "id" => $this->clientCVFormOne->profileForm->id,
                "name" => $this->clientCVFormOne->profileForm->form->name,
                "description" => $this->clientCVFormOne->profileForm->form->description,
                "stringFields" => [],
                "integerFields" => [],
                "textAreaFields" => [],
                "attachmentFields" => [],
                "singleSelectFields" => [],
                "multiSelectFields" => [],
            ],
        ];
        
        $uri = $this->clientCVFormUri . "/{$this->clientCVFormOne->id}";
        $this->get($uri, $this->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_showAll_200()
    {
        $response = [
            "total" => 1,
            "list" => [
                [
                    "id" => $this->clientCVFormOne->id,
                    "profileForm" => [
                        "id" => $this->clientCVFormOne->profileForm->id,
                        "name" => $this->clientCVFormOne->profileForm->form->name,
                    ],
                ],
            ],
        ];
        $this->get($this->clientCVFormUri, $this->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
}
