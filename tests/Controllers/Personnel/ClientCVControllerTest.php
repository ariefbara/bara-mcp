<?php

namespace Tests\Controllers\Personnel;

use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientCV;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClientCVForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProfileForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;

class ClientCVControllerTest extends PersonnelTestCase
{
    protected $clientCVUri;
    protected $client;
    protected $clientCVOne;
    protected $clientCVTwo_removed;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->clientCVUri = $this->personnelUri . "/client-cvs";
        
        $this->connection->table("Form")->truncate();
        $this->connection->table("Client")->truncate();
        $this->connection->table("ProfileForm")->truncate();
        $this->connection->table("ClientCVForm")->truncate();
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("ClientCV")->truncate();
        
        $firm = $this->personnel->firm;
        
        $this->client = new RecordOfClient($firm, 0);
        $this->connection->table("Client")->insert($this->client->toArrayForDbEntry());
        
        $form = new RecordOfForm(0);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());
        
        $profileForm = new RecordOfProfileForm($firm, $form);
        $this->connection->table("ProfileForm")->insert($profileForm->toArrayForDbEntry());
        
        $clientCVFormOne = new RecordOfClientCVForm($firm, $profileForm, 1);
        $clientCVFormTwo = new RecordOfClientCVForm($firm, $profileForm, 2);
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
        $this->connection->table("Client")->truncate();
        $this->connection->table("ProfileForm")->truncate();
        $this->connection->table("ClientCVForm")->truncate();
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("ClientCV")->truncate();
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
        $uri = $this->clientCVUri . "/{$this->client->id}/{$this->clientCVOne->clientCVForm->id}";
        $this->get($uri, $this->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    
    public function test_showAll_200()
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
        $uri = $this->clientCVUri . "/{$this->client->id}";
        $this->get($uri, $this->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
}
