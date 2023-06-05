<?php

namespace Tests\Controllers\Client;

use DateTimeImmutable;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientBio;
use Tests\Controllers\RecordPreparation\Firm\RecordOfBioForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClientCVForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;

class ClientBioControllerTest extends ClientTestCase
{
    protected $clientBioUri;
    protected $clientBioOne;
    protected $clientBioTwo_removed;
    protected $bioForm;
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
        $this->clientBioUri = $this->clientUri . "/bios";
        
        $this->connection->table("Form")->truncate();
        $this->connection->table("ProfileForm")->truncate();
        $this->connection->table("BioForm")->truncate();
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("ClientBio")->truncate();
        
        $firm = $this->client->firm;
        
        $form = new RecordOfForm(0);
        $formOne = new RecordOfForm(1);
        $formTwo = new RecordOfForm(2);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());
        $this->connection->table("Form")->insert($formOne->toArrayForDbEntry());
        $this->connection->table("Form")->insert($formTwo->toArrayForDbEntry());
        
        $this->bioForm = new RecordOfBioForm($firm, $form);
        $bioFormOne = new RecordOfBioForm($firm, $formOne);
        $bioFormTwo = new RecordOfBioForm($firm, $formTwo);
        $this->connection->table("BioForm")->insert($this->bioForm->toArrayForDbEntry());
        $this->connection->table("BioForm")->insert($bioFormOne->toArrayForDbEntry());
        $this->connection->table("BioForm")->insert($bioFormTwo->toArrayForDbEntry());
        
        $formRecordOne = new RecordOfFormRecord($form, 1);
        $formRecordTwo = new RecordOfFormRecord($form, 2);
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
        $this->connection->table("ProfileForm")->truncate();
        $this->connection->table("BioForm")->truncate();
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("ClientBio")->truncate();
    }
    
    public function test_submit_200()
    {
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("ClientBio")->truncate();
        
        
        $uri = $this->clientBioUri . "/{$this->bioForm->form->id}";
        $this->put($uri, $this->submitInput, $this->client->token)
                ->seeStatusCode(200);
        
        $response = [
            "submitTime" => (new DateTimeImmutable())->format("Y-m-d H:i:s"),
            "bioForm" => [
                "id" => $this->bioForm->form->id,
                "name" => $this->bioForm->form->name,
            ],
        ];
        $this->seeJsonContains($response);
        
        $clientBioEntry = [
            "Client_id" => $this->client->id,
            "BioForm_id" => $this->bioForm->form->id,
            "removed" => false
        ];
        $this->seeInDatabase("ClientBio", $clientBioEntry);
        
        $formRecordEntry = [
            "submitTime" => (new DateTimeImmutable())->format("Y-m-d H:i:s"),
            "Form_id" => $this->bioForm->form->id,
        ];
        $this->seeInDatabase("FormRecord", $formRecordEntry);
    }
    
    public function test_remove_200()
    {
        $uri = $this->clientBioUri . "/{$this->clientBioOne->bioForm->form->id}";
        $this->delete($uri, [], $this->client->token)
                ->seeStatusCode(200);
        
        $clientBioEntry = [
            "id" => $this->clientBioOne->formRecord->id,
            "removed" => true,
        ];
        $this->seeInDatabase("ClientBio", $clientBioEntry);
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->clientBioOne->formRecord->id,
            "bioForm" => [
                "name" => $this->clientBioOne->bioForm->form->name,
                "description" => $this->clientBioOne->bioForm->form->description,
                'sections' => [],
                'stringFields' => [],
                'integerFields' => [],
                'textAreaFields' => [],
                'attachmentFields' => [],
                'singleSelectFields' => [],
                'multiSelectFields' => [],
            ],
            "submitTime" => $this->clientBioOne->formRecord->submitTime,
            "stringFieldRecords" => [],
            "integerFieldRecords" => [],
            "textAreaFieldRecords" => [],
            "attachmentFieldRecords" => [],
            "singleSelectFieldRecords" => [],
            "multiSelectFieldRecords" => [],
        ];
        $uri = $this->clientBioUri . "/{$this->clientBioOne->bioForm->form->id}";
        $this->get($uri, $this->client->token)
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
                    ],
                    "submitTime" => $this->clientBioOne->formRecord->submitTime,
                ],
            ],
        ];
        $this->get($this->clientBioUri, $this->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
}
