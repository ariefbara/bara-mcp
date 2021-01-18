<?php

namespace Tests\Controllers\Client;

use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientCV;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClientCVForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProfileForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;

class ClientCVControllerTest extends ClientTestCase
{
    protected $clientCVUri;
    protected $clientCVOne;
    protected $clientCVTwo_removed;
    protected $clientCVForm;
    protected $submitInput = [
        "stringFieldRecords" => [],
        "integerFieldRecords" => [],
        "textAreaFieldRecords" => [],
        "attachmentFieldRecords" => [],
        "singleSelectFieldRecords" => [],
        "multiSelectFieldRecords" => [],
    ];
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->clientCVUri = $this->clientUri . "/cv";
        
        $this->connection->table("Form")->truncate();
        $this->connection->table("ProfileForm")->truncate();
        $this->connection->table("ClientCVForm")->truncate();
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("ClientCV")->truncate();
        
        $firm = $this->client->firm;
        
        $form = new RecordOfForm(0);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());
        
        $profileForm = new RecordOfProfileForm($firm, $form);
        $this->connection->table("ProfileForm")->insert($profileForm->toArrayForDbEntry());
        
        $this->clientCVForm = new RecordOfClientCVForm($firm, $profileForm, 0);
        $clientCVFormOne = new RecordOfClientCVForm($firm, $profileForm, 1);
        $clientCVFormTwo = new RecordOfClientCVForm($firm, $profileForm, 2);
        $this->connection->table("ClientCVForm")->insert($this->clientCVForm->toArrayForDbEntry());
        $this->connection->table("ClientCVForm")->insert($clientCVFormOne->toArrayForDbEntry());
        $this->connection->table("ClientCVForm")->insert($clientCVFormTwo->toArrayForDbEntry());
        
        $formRecordOne = new RecordOfFormRecord($form, 1);
        $formRecordTwo = new RecordOfFormRecord($form, 2);
        $this->connection->table("FormRecord")->insert($formRecordOne->toArrayForDbEntry());
        $this->connection->table("FormRecord")->insert($formRecordTwo->toArrayForDbEntry());
        
        $this->clientCVOne = new RecordOfClientCV($this->client, $clientCVFormOne, $formRecordOne);
        $this->clientCVTwo_removed = new RecordOfClientCV($this->client, $clientCVFormTwo, $formRecordTwo);
        $this->clientCVTwo_removed->removed = true;
        $this->connection->table("ClientCV")->insert($this->clientCVOne->toArrayForDbEntry());
        $this->connection->table("ClientCV")->insert($this->clientCVTwo_removed->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Form")->truncate();
        $this->connection->table("ProfileForm")->truncate();
        $this->connection->table("ClientCVForm")->truncate();
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("ClientCV")->truncate();
    }
    
    public function test_submit_200()
    {
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("ClientCV")->truncate();
        
        $response = [
            "submitTime" => (new \DateTimeImmutable())->format("Y-m-d H:i:s"),
            "clientCVForm" => [
                "id" => $this->clientCVForm->id,
                "profileForm" => [
                    "id" => $this->clientCVForm->profileForm->id,
                    "name" => $this->clientCVForm->profileForm->form->name,
                ],
            ],
        ];
        
        $uri = $this->clientCVUri . "/{$this->clientCVForm->id}";
        $this->put($uri, $this->submitInput, $this->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
        
        $clientCVEntry = [
            "Client_id" => $this->client->id,
            "ClientCVForm_id" => $this->clientCVForm->id,
            "removed" => false
        ];
        $this->seeInDatabase("ClientCV", $clientCVEntry);
        
        $formRecordEntry = [
            "submitTime" => (new \DateTimeImmutable())->format("Y-m-d H:i:s"),
            "Form_id" => $this->clientCVForm->profileForm->form->id,
        ];
        $this->seeInDatabase("FormRecord", $formRecordEntry);
    }
    public function test_remove_200()
    {
        $uri = $this->clientCVUri . "/{$this->clientCVOne->clientCVForm->id}";
        $this->delete($uri, [], $this->client->token)
                ->seeStatusCode(200);
        
        $clientCVEntry = [
            "id" => $this->clientCVOne->id,
            "removed" => true,
        ];
        $this->seeInDatabase("ClientCV", $clientCVEntry);
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->clientCVOne->id,
            "clientCVForm" => [
                "id" => $this->clientCVOne->clientCVForm->id,
                "profileForm" => [
                    "id" => $this->clientCVOne->clientCVForm->profileForm->id,
                    "name" => $this->clientCVOne->clientCVForm->profileForm->form->name,
                ],
            ],
            "submitTime" => $this->clientCVOne->formRecord->submitTime,
            "stringFieldRecords" => [],
            "integerFieldRecords" => [],
            "textAreaFieldRecords" => [],
            "attachmentFieldRecords" => [],
            "singleSelectFieldRecords" => [],
            "multiSelectFieldRecords" => [],
        ];
        $uri = $this->clientCVUri . "/{$this->clientCVOne->clientCVForm->id}";
        $this->get($uri, $this->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    
    public function test_showAll_scenario_expectedResult()
    {
        $response = [
            "total" => 1,
            "list" => [
                [
                    "id" => $this->clientCVOne->id,
                    "clientCVForm" => [
                        "id" => $this->clientCVOne->clientCVForm->id,
                        "profileForm" => [
                            "id" => $this->clientCVOne->clientCVForm->profileForm->id,
                            "name" => $this->clientCVOne->clientCVForm->profileForm->form->name,
                        ],
                    ],
                    "submitTime" => $this->clientCVOne->formRecord->submitTime,
                ],
            ],
        ];
        $this->get($this->clientCVUri, $this->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
}
