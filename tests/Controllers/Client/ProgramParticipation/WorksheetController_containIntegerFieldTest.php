<?php

namespace Tests\Controllers\Client\ProgramParticipation;

use Tests\Controllers\RecordPreparation\Shared\ {
    Form\RecordOfIntegerField,
    FormRecord\RecordOfIntegerFieldRecord
};

class WorksheetController_containIntegerFieldTest extends WorksheetTestCase
{
    protected $integerFieldOne, $integerFieldTwo_required, $integerFieldThree_removed;
    protected $integerFieldRecordOne, $integerFieldRecordThree;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('IntegerField')->truncate();
        $this->connection->table('IntegerFieldRecord')->truncate();
        
        $this->integerFieldOne = new RecordOfIntegerField($this->form, 1);
        $this->integerFieldOne->minValue = 11;
        $this->integerFieldOne->maxValue = 999;
        
        $this->integerFieldTwo_required = new RecordOfIntegerField($this->form, 2);
        $this->integerFieldTwo_required->mandatory = true;
        
        $this->integerFieldThree_removed = new RecordOfIntegerField($this->form, 3);
        $this->integerFieldThree_removed->removed = true;
        
        $this->connection->table('IntegerField')->insert($this->integerFieldOne->toArrayForDbEntry());
        $this->connection->table('IntegerField')->insert($this->integerFieldTwo_required->toArrayForDbEntry());
        $this->connection->table('IntegerField')->insert($this->integerFieldThree_removed->toArrayForDbEntry());
        
        $this->integerFieldRecordOne = new RecordOfIntegerFieldRecord($this->worksheet->formRecord, $this->integerFieldOne, 1);
        $this->integerFieldRecordThree = new RecordOfIntegerFieldRecord($this->worksheet->formRecord, $this->integerFieldThree_removed, 3);
        $this->connection->table('IntegerFieldRecord')->insert($this->integerFieldRecordOne->toArrayForDbEntry());
        $this->connection->table('IntegerFieldRecord')->insert($this->integerFieldRecordThree->toArrayForDbEntry());
        
        $this->worksheetInput['integerFieldRecords'] = [
            [
                "fieldId" => $this->integerFieldOne->id,
                "value" => 456,
            ],
            [
                "fieldId" => $this->integerFieldTwo_required->id,
                "value" => 432,
            ],
        ];
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('IntegerField')->truncate();
        $this->connection->table('IntegerFieldRecord')->truncate();
    }
    
    public function test_addRoot()
    {
        $fieldRecordOneResponse = [
            "integerField" => [
                "id" => $this->integerFieldOne->id,
                "name" => $this->integerFieldOne->name,
                "position" => $this->integerFieldOne->position,
            ],
            "value" => $this->worksheetInput['integerFieldRecords'][0]['value'],
        ];
        $fieldRecordTwoResponse = [
            "integerField" => [
                "id" => $this->integerFieldTwo_required->id,
                "name" => $this->integerFieldTwo_required->name,
                "position" => $this->integerFieldTwo_required->position,
            ],
            "value" => $this->worksheetInput['integerFieldRecords'][1]['value'],
        ];
        
        $this->post($this->worksheetUri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(201)
                ->seeJsonContains($fieldRecordOneResponse)
                ->seeJsonContains($fieldRecordTwoResponse);
        
        $fieldOneRecordEntry = [
            "IntegerField_id" => $this->integerFieldOne->id,
            "value" => $this->worksheetInput['integerFieldRecords'][0]['value'],
        ];
        $this->seeInDatabase('IntegerFieldRecord', $fieldOneRecordEntry);
        $fieldTwoRecordEntry = [
            "IntegerField_id" => $this->integerFieldTwo_required->id,
            "value" => $this->worksheetInput['integerFieldRecords'][1]['value'],
        ];
        $this->seeInDatabase('IntegerFieldRecord', $fieldTwoRecordEntry);
    }
    public function test_addRoot_emptyInputForMandatoryField_error400()
    {
        $this->worksheetInput['integerFieldRecords'][1]['value'] = 0;
        $this->post($this->worksheetUri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(400);
    }
    public function test_addRoot_fieldRecordInputLessThanMinValue_error400()
    {
        $this->worksheetInput['integerFieldRecords'][0]['value'] = 5;
        $this->post($this->worksheetUri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(400);
    }
    public function test_addRoot_fieldRecordInputGreaterThanMaxValue_error400()
    {
        $this->worksheetInput['integerFieldRecords'][0]['value'] = 99999999999;
        $this->post($this->worksheetUri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(400);
    }
    public function test_addRoot_fieldRecordInputContainNonIntegerValue_castValueToZero()
    {
        $this->worksheetInput['integerFieldRecords'][0]['value'] = 'containString234';
        $this->post($this->worksheetUri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(201);
        
        $fieldOneRecordEntry = [
            "IntegerField_id" => $this->integerFieldOne->id,
            "value" => 0,
        ];
        $this->seeInDatabase('IntegerFieldRecord', $fieldOneRecordEntry);
    }
    public function test_show_includeIntegerFieldRecord()
    {
        $this->worksheet->formRecordResponse['integerFieldRecords'] = [
            [
                "id" => $this->integerFieldRecordOne->id,
                "integerField" => [
                    "id" => $this->integerFieldRecordOne->integerField->id,
                    "name" => $this->integerFieldRecordOne->integerField->name,
                    "position" => $this->integerFieldRecordOne->integerField->position,
                ],
                "value" => $this->integerFieldRecordOne->value,
            ],
            [
                "id" => $this->integerFieldRecordThree->id,
                "integerField" => [
                    "id" => $this->integerFieldRecordThree->integerField->id,
                    "name" => $this->integerFieldRecordThree->integerField->name,
                    "position" => $this->integerFieldRecordThree->integerField->position,
                ],
                "value" => $this->integerFieldRecordThree->value,
            ],
        ];
        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->get($uri, $this->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($this->worksheet->formRecordResponse);
    }
    
    public function test_update_updateExistingFieldRecord()
    {
        $fieldRecordOneResponse = [
            "id" => $this->integerFieldRecordOne->id,
            "integerField" => [
                "id" => $this->integerFieldOne->id,
                "name" => $this->integerFieldOne->name,
                "position" => $this->integerFieldOne->position,
            ],
            "value" => $this->worksheetInput['integerFieldRecords'][0]['value'],
        ];
            
        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->patch($uri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($fieldRecordOneResponse);
        
        $fieldOneRecordEntry = [
            "FormRecord_id" => $this->integerFieldRecordOne->formRecord->id,
            "id" => $this->integerFieldRecordOne->id,
            "IntegerField_id" => $this->integerFieldOne->id,
            "value" => $this->worksheetInput['integerFieldRecords'][0]['value'],
        ];
        $this->seeInDatabase('IntegerFieldRecord', $fieldOneRecordEntry);
    }
    public function test_update_formHasFieldNotCorrespontToAnyFieldRecordInWorksheet_addAsNewFieldRecord()
    {
        $fieldRecordTwoResponse = [
            "integerField" => [
                "id" => $this->integerFieldTwo_required->id,
                "name" => $this->integerFieldTwo_required->name,
                "position" => $this->integerFieldTwo_required->position,
            ],
            "value" => $this->worksheetInput['integerFieldRecords'][1]['value'],
        ];
        
        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->patch($uri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($fieldRecordTwoResponse);
        
        $fieldTwoRecordEntry = [
            "IntegerField_id" => $this->integerFieldTwo_required->id,
            "value" => $this->worksheetInput['integerFieldRecords'][1]['value'],
        ];
        $this->seeInDatabase('IntegerFieldRecord', $fieldTwoRecordEntry);
    }
    public function test_update_removeFieldRecordReferToRemovedField()
    {
        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->patch($uri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(200);
        $fieldThreeRecordEntry = [
            "id" => $this->integerFieldRecordThree->id,
            "removed" => true,
        ];
        $this->seeInDatabase('IntegerFieldRecord', $fieldThreeRecordEntry);
    }
    
}
