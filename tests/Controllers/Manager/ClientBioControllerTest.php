<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientBio;
use Tests\Controllers\RecordPreparation\Firm\RecordOfBioForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;

class ClientBioControllerTest extends ManagerTestCase
{
    protected $clientBioUri;
    protected $client;
    protected $clientBioOne;
    protected $clientBioTwo_removed;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->clientBioUri = $this->managerUri . "/client-bios";
        
        $this->connection->table("Form")->truncate();
        $this->connection->table("Client")->truncate();
        $this->connection->table("BioForm")->truncate();
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("ClientBio")->truncate();
        
        $firm = $this->manager->firm;
        
        $this->client = new RecordOfClient($firm, 0);
        $this->connection->table("Client")->insert($this->client->toArrayForDbEntry());
        
        $formOne = new RecordOfForm(1);
        $formTwo = new RecordOfForm(2);
        $this->connection->table("Form")->insert($formOne->toArrayForDbEntry());
        $this->connection->table("Form")->insert($formTwo->toArrayForDbEntry());
        
        $bioFormOne = new RecordOfBioForm($firm, $formOne);
        $bioFormTwo = new RecordOfBioForm($firm, $formTwo);
        $this->connection->table("BioForm")->insert($bioFormOne->toArrayForDbEntry());
        $this->connection->table("BioForm")->insert($bioFormTwo->toArrayForDbEntry());
        
        $formRecordOne = new RecordOfFormRecord($formOne, 1);
        $formRecordTwo = new RecordOfFormRecord($formOne, 2);
        $this->connection->table("FormRecord")->insert($formRecordOne->toArrayForDbEntry());
        $this->connection->table("FormRecord")->insert($formRecordTwo->toArrayForDbEntry());
        
        $this->clientBioOne = new RecordOfClientBio($this->client, $bioFormOne, $formRecordOne);
        $this->clientBioTwo_removed = new RecordOfClientBio($this->client, $bioFormTwo, $formRecordTwo);
        $this->clientBioTwo_removed->removed = true;
        $this->connection->table("ClientBio")->insert($this->clientBioOne->toArrayForDbEntry());
        $this->connection->table("ClientBio")->insert($this->clientBioTwo_removed->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Form")->truncate();
        $this->connection->table("Client")->truncate();
        $this->connection->table("BioForm")->truncate();
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("ClientBio")->truncate();
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->clientBioOne->formRecord->id,
            "bioForm" => [
                "id" => $this->clientBioOne->bioForm->form->id,
                "name" => $this->clientBioOne->bioForm->form->name,
                "disabled" => $this->clientBioOne->bioForm->disabled,
            ],
            "submitTime" => $this->clientBioOne->formRecord->submitTime,
            "stringFieldRecords" => [],
            "integerFieldRecords" => [],
            "textAreaFieldRecords" => [],
            "attachmentFieldRecords" => [],
            "singleSelectFieldRecords" => [],
            "multiSelectFieldRecords" => [],
        ];
        $uri = $this->clientBioUri . "/{$this->client->id}/{$this->clientBioOne->bioForm->form->id}";
        $this->get($uri, $this->manager->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 1,
            "list" => [
                [
                    "id" => $this->clientBioOne->formRecord->id,
                    "bioForm" => [
                        "id" => $this->clientBioOne->bioForm->form->id,
                        "name" => $this->clientBioOne->bioForm->form->name,
                        "disabled" => $this->clientBioOne->bioForm->disabled,
                    ],
                    "submitTime" => $this->clientBioOne->formRecord->submitTime,
                ],
            ],
        ];
        $uri = $this->clientBioUri . "/{$this->client->id}";
        $this->get($uri, $this->manager->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
}
