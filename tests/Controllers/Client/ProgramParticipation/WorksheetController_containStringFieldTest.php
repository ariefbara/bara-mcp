<?php

namespace Tests\Controllers\Client\ProgramParticipation;

use Tests\Controllers\RecordPreparation\Shared\ {
    Form\RecordOfStringField,
    FormRecord\RecordOfStringFieldRecord
};

class WorksheetController_containStringFieldTest extends WorksheetTestCase
{
    protected $stringFieldOne, $stringFieldTwo_required, $stringFieldThree_removed;
    protected $stringFieldRecordOne, $stringFieldRecordThree;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('StringField')->truncate();
        $this->connection->table('StringFieldRecord')->truncate();
        
        $this->stringFieldOne = new RecordOfStringField($this->form, 1);
        $this->stringFieldOne->minValue = 16;
        $this->stringFieldOne->maxValue = 32;
        $this->stringFieldTwo_required = new RecordOfStringField($this->form, 2);
        $this->stringFieldTwo_required->mandatory = true;
        $this->stringFieldThree_removed = new RecordOfStringField($this->form, 3);
        $this->stringFieldThree_removed->removed = true;
        
        $this->connection->table('StringField')->insert($this->stringFieldOne->toArrayForDbEntry());
        $this->connection->table('StringField')->insert($this->stringFieldTwo_required->toArrayForDbEntry());
        $this->connection->table('StringField')->insert($this->stringFieldThree_removed->toArrayForDbEntry());
        
        $this->stringFieldRecordOne = new RecordOfStringFieldRecord($this->worksheet->formRecord, $this->stringFieldOne, 1);
        $this->stringFieldRecordThree = new RecordOfStringFieldRecord($this->worksheet->formRecord, $this->stringFieldThree_removed, 3);
        $this->connection->table('StringFieldRecord')->insert($this->stringFieldRecordOne->toArrayForDbEntry());
        $this->connection->table('StringFieldRecord')->insert($this->stringFieldRecordThree->toArrayForDbEntry());
        
        $this->worksheetInput['stringFieldRecords'] = [
            [
                "fieldId" => $this->stringFieldOne->id,
                "value" => 'string field one input',
            ],
            [
                "fieldId" => $this->stringFieldTwo_required->id,
                "value" => 'string field two input',
            ],
        ];
        
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('StringField')->truncate();
        $this->connection->table('StringFieldRecord')->truncate();
    }
    
    public function test_submit()
    {
        $stringFieldRecordOneResponse = [
            "stringField" => [
                "id" => $this->stringFieldOne->id,
                "name" => $this->stringFieldOne->name,
                "position" => $this->stringFieldOne->position,
            ],
            "value" => $this->worksheetInput['stringFieldRecords'][0]['value'],
        ];
        $stringFieldRecordTwoResponse = [
            "stringField" => [
                "id" => $this->stringFieldTwo_required->id,
                "name" => $this->stringFieldTwo_required->name,
                "position" => $this->stringFieldTwo_required->position,
            ],
            "value" => $this->worksheetInput['stringFieldRecords'][1]['value'],
        ];
        
        $this->post($this->worksheetUri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(201)
                ->seeJsonContains($stringFieldRecordOneResponse)
                ->seeJsonContains($stringFieldRecordTwoResponse);
        
        $stringFieldRecordOneEntry = [
            "StringField_id" => $this->stringFieldOne->id,
            "value" => $this->worksheetInput['stringFieldRecords'][0]['value'],
        ];
        $this->seeInDatabase("StringFieldRecord", $stringFieldRecordOneEntry);
        $stringFieldRecordTwoEntry = [
            "StringField_id" => $this->stringFieldTwo_required->id,
            "value" => $this->worksheetInput['stringFieldRecords'][1]['value'],
        ];
        $this->seeInDatabase("StringFieldRecord", $stringFieldRecordTwoEntry);
    }
    public function test_submit_emptyStringFieldRecordInputForMandatoryField_error400()
    {
        $this->worksheetInput['stringFieldRecords'][1]['value'] = null;
        $this->post($this->worksheetUri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(400);
    }
    public function test_submit_stringFieldRecordInputLengtLessThanMinValue_error400()
    {
        $this->worksheetInput['stringFieldRecords'][0]['value'] = 'to short';
        $this->post($this->worksheetUri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(400);
    }
    public function test_submit_stringFieldRecordInputLenghtGreaterThanMaxValue_error400()
    {
        $this->worksheetInput['stringFieldRecords'][0]['value'] = 'to loooooooooooooooooooooooooooooooooooooooooooooooong';
        $this->post($this->worksheetUri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(400);
    }
    
    public function test_show()
    {
        $this->worksheet->formRecordResponse['id'] = $this->worksheet->formRecord->id;
        $this->worksheet->formRecordResponse['submitTime'] = $this->worksheet->formRecord->submitTime;
        $this->profileResponse['stringFieldRecords'] = [
            [
                "id" => $this->stringFieldRecordOne->id,
                "stringField" => [
                    "id" => $this->stringFieldRecordOne->stringField->id,
                    "name" => $this->stringFieldRecordOne->stringField->name,
                    "position" => $this->stringFieldRecordOne->stringField->position,
                ],
                "value" => $this->stringFieldRecordOne->value,
            ],
            [
                "id" => $this->stringFieldRecordThree->id,
                "stringField" => [
                    "id" => $this->stringFieldRecordThree->stringField->id,
                    "name" => $this->stringFieldRecordThree->stringField->name,
                    "position" => $this->stringFieldRecordThree->stringField->position,
                ],
                "value" => $this->stringFieldRecordThree->value,
            ],
        ];
        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->get($uri, $this->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($this->profileResponse);
    }
    
    
    public function test_update_updateExistingStringFieldRecord()
    {
        $stringFieldRecordResponse = [
            "id" => $this->stringFieldRecordOne->id,
            "stringField" => [
                "id" => $this->stringFieldOne->id,
                "name" => $this->stringFieldOne->name,
                "position" => $this->stringFieldOne->position,
            ],
            "value" => $this->worksheetInput['stringFieldRecords'][0]['value'],
        ];
        
        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->patch($uri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($stringFieldRecordResponse);
        
        $stringFieldRecordOneEntry = [
            "id" => $this->stringFieldRecordOne->id,
            "StringField_id" => $this->stringFieldOne->id,
            "value" => $this->worksheetInput['stringFieldRecords'][0]['value'],
        ];
        $this->seeInDatabase("StringFieldRecord", $stringFieldRecordOneEntry);
    }
    public function test_update_noFieldRecordReferToField_addNewFieldRecord()
    {
        $stringFieldRecordResponse = [
            "stringField" => [
                "id" => $this->stringFieldTwo_required->id,
                "name" => $this->stringFieldTwo_required->name,
                "position" => $this->stringFieldTwo_required->position,
            ],
            "value" => $this->worksheetInput['stringFieldRecords'][1]['value'],
        ];
        
        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->patch($uri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($stringFieldRecordResponse);
        
        $stringFieldRecordEntry = [
            "StringField_id" => $this->stringFieldTwo_required->id,
            "value" => $this->worksheetInput['stringFieldRecords'][1]['value'],
        ];
        $this->seeInDatabase("StringFieldRecord", $stringFieldRecordEntry);
    }
    public function test_update_hasFieldRecordReferToRemovedField_removeFieldRecord()
    {
        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->patch($uri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(200);
        $stringFieldRecordEntry = [
            "id" => $this->stringFieldRecordThree->id,
            "removed" => true,
        ];
        $this->seeInDatabase("StringFieldRecord", $stringFieldRecordEntry);
    }
}
