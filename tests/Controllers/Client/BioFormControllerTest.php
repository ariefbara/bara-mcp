<?php

namespace Tests\Controllers\Client;

use Tests\Controllers\RecordPreparation\Firm\RecordOfBioForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;

class BioFormControllerTest extends ClientTestCase
{
    protected $bioFormUri;
    protected $bioFormOne;
    protected $bioFormTwo_disabled;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->bioFormUri = $this->clientUri . "/bio-forms";
        
        $this->connection->table("Form")->truncate();
        $this->connection->table("BioForm")->truncate();
        
        $firm = $this->client->firm;
        
        $formOne = new RecordOfForm(1);
        $formTwo = new RecordOfForm(2);
        $this->connection->table("Form")->insert($formOne->toArrayForDbEntry());
        $this->connection->table("Form")->insert($formTwo->toArrayForDbEntry());
        
        $this->bioFormOne = new RecordOfBioForm($firm, $formOne);
        $this->bioFormTwo_disabled= new RecordOfBioForm($firm, $formTwo);
        $this->bioFormTwo_disabled->disabled = true;
        $this->connection->table("BioForm")->insert($this->bioFormOne->toArrayForDbEntry());
        $this->connection->table("BioForm")->insert($this->bioFormTwo_disabled->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Form")->truncate();
        $this->connection->table("BioForm")->truncate();
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->bioFormOne->form->id,
            "name" => $this->bioFormOne->form->name,
            "description" => $this->bioFormOne->form->description,
            "stringFields" => [],
            "integerFields" => [],
            "textAreaFields" => [],
            "attachmentFields" => [],
            "singleSelectFields" => [],
            "multiSelectFields" => [],
        ];
        
        $uri = $this->bioFormUri . "/{$this->bioFormOne->form->id}";
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
                    "id" => $this->bioFormOne->form->id,
                    "name" => $this->bioFormOne->form->name,
                    "description" => $this->bioFormOne->form->description,
                ],
            ],
        ];
        $this->get($this->bioFormUri, $this->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
}
