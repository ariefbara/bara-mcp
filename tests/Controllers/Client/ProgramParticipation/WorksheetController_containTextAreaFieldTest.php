<?php

namespace Tests\Controllers\Client\ProgramParticipation;

use Tests\Controllers\RecordPreparation\Shared\ {
    Form\RecordOfTextAreaField,
    FormRecord\RecordOfTextAreaFieldRecord
};

class WorksheetController_containTextAreaFieldTest extends WorksheetTestCase
{
    protected $fieldOne, $fieldTwo_required, $fieldThree_removed;
    protected $fieldRecordOne, $fieldRecordThree;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('TextAreaField')->truncate();
        $this->connection->table('TextAreaFieldRecord')->truncate();
        
        $this->fieldOne = new RecordOfTextAreaField($this->form, 1);
        $this->fieldOne->minValue = 16;
        $this->fieldOne->maxValue = 32;
        
        $this->fieldTwo_required = new RecordOfTextAreaField($this->form, 2);
        $this->fieldTwo_required->mandatory = true;
        
        $this->fieldThree_removed = new RecordOfTextAreaField($this->form, 3);
        $this->fieldThree_removed->removed = true;
        
        $this->connection->table('TextAreaField')->insert($this->fieldOne->toArrayForDbEntry());
        $this->connection->table('TextAreaField')->insert($this->fieldTwo_required->toArrayForDbEntry());
        $this->connection->table('TextAreaField')->insert($this->fieldThree_removed->toArrayForDbEntry());
        
        $this->fieldRecordOne = new RecordOfTextAreaFieldRecord($this->worksheet->formRecord, $this->fieldOne, 1);
        $this->fieldRecordThree = new RecordOfTextAreaFieldRecord($this->worksheet->formRecord, $this->fieldThree_removed, 3);
        
        $this->connection->table('TextAreaFieldRecord')->insert($this->fieldRecordOne->toArrayForDbEntry());
        $this->connection->table('TextAreaFieldRecord')->insert($this->fieldRecordThree->toArrayForDbEntry());
        
        $this->worksheetInput['textAreaFieldRecords'] = [
            [
                "fieldId" => $this->fieldOne->id,
                "value" => 'text area one input',
            ],
            [
                "fieldId" => $this->fieldTwo_required->id,
                "value" => 'text area two input',
            ],
        ];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('TextAreaField')->truncate();
        $this->connection->table('TextAreaFieldRecord')->truncate();
    }
    public function test_submit()
    {
        $fieldRecordOneResponse = [
            "textAreaField" => [
                "id" => $this->fieldOne->id,
                "name" => $this->fieldOne->name,
                "position" => $this->fieldOne->position,
            ],
            "value" => $this->worksheetInput['textAreaFieldRecords'][0]['value'],
        ];
        $fieldRecordTwoResponse = [
            "textAreaField" => [
                "id" => $this->fieldTwo_required->id,
                "name" => $this->fieldTwo_required->name,
                "position" => $this->fieldTwo_required->position,
            ],
            "value" => $this->worksheetInput['textAreaFieldRecords'][1]['value'],
        ];
        
        $this->post($this->worksheetUri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(201)
                ->seeJsonContains($fieldRecordOneResponse)
                ->seeJsonContains($fieldRecordTwoResponse);
        
        $fieldOneRecordEntry = [
            "TextAreaField_id" => $this->fieldOne->id,
            "value" => $this->worksheetInput['textAreaFieldRecords'][0]['value'],
        ];
        $this->seeInDatabase('TextAreaFieldRecord', $fieldOneRecordEntry);
        $fieldTwoRecordEntry = [
            "TextAreaField_id" => $this->fieldTwo_required->id,
            "value" => $this->worksheetInput['textAreaFieldRecords'][1]['value'],
        ];
        $this->seeInDatabase('TextAreaFieldRecord', $fieldTwoRecordEntry);
    }
    
    public function test_submit_emptyInputForMandatoryField_error400()
    {
        $this->worksheetInput['textAreaFieldRecords'][1]['value'] = '';
        $this->post($this->worksheetUri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(400);
    }
    public function test_submit_fieldRecordInputLessThanMinValue_error400()
    {
        $this->worksheetInput['textAreaFieldRecords'][0]['value'] = 'to short';
        $this->post($this->worksheetUri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(400);
    }
    public function test_submit_fieldRecordInputGreaterThanMaxValue_error400()
    {
        $this->worksheetInput['textAreaFieldRecords'][0]['value'] = 'to loooooooooooooooooooooooooooooooooooooooooooooooong';
        $this->post($this->worksheetUri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(400);
    }
    
    public function test_show()
    {
        $this->worksheet->formRecordResponse['textAreaFieldRecords'] = [
            [
                "id" => $this->fieldRecordOne->id,
                "textAreaField" => [
                    "id" => $this->fieldRecordOne->textAreaField->id,
                    "name" => $this->fieldRecordOne->textAreaField->name,
                    "position" => $this->fieldRecordOne->textAreaField->position,
                ],
                "value" => $this->fieldRecordOne->value,
            ],
            [
                "id" => $this->fieldRecordThree->id,
                "textAreaField" => [
                    "id" => $this->fieldRecordThree->textAreaField->id,
                    "name" => $this->fieldRecordThree->textAreaField->name,
                    "position" => $this->fieldRecordThree->textAreaField->position,
                ],
                "value" => $this->fieldRecordThree->value,
            ],
        ];
        $uri = $this->worksheetUri . "/{$this->worksheet->formRecord->id}";
        $this->get($uri, $this->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($this->worksheet->formRecordResponse);
    }
    
    public function test_update_updateExistingFieldRecord()
    {
        $fieldRecordOneResponse = [
            "id" => $this->fieldRecordOne->id,
            "textAreaField" => [
                "id" => $this->fieldOne->id,
                "name" => $this->fieldOne->name,
                "position" => $this->fieldOne->position,
            ],
            "value" => $this->worksheetInput['textAreaFieldRecords'][0]['value'],
        ];
            
        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->patch($uri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($fieldRecordOneResponse);
        
        $fieldOneRecordEntry = [
            "FormRecord_id" => $this->fieldRecordOne->formRecord->id,
            "id" => $this->fieldRecordOne->id,
            "TextAreaField_id" => $this->fieldOne->id,
            "value" => $this->worksheetInput['textAreaFieldRecords'][0]['value'],
        ];
        $this->seeInDatabase('TextAreaFieldRecord', $fieldOneRecordEntry);
    }
    public function test_update_noFieldRecordReferToField_addNewFieldRecord()
    {
        $fieldRecordTwoResponse = [
            "textAreaField" => [
                "id" => $this->fieldTwo_required->id,
                "name" => $this->fieldTwo_required->name,
                "position" => $this->fieldTwo_required->position,
            ],
            "value" => $this->worksheetInput['textAreaFieldRecords'][1]['value'],
        ];
        
        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->patch($uri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($fieldRecordTwoResponse);
        
        $fieldTwoRecordEntry = [
            "TextAreaField_id" => $this->fieldTwo_required->id,
            "value" => $this->worksheetInput['textAreaFieldRecords'][1]['value'],
        ];
        $this->seeInDatabase('TextAreaFieldRecord', $fieldTwoRecordEntry);
    }
    public function test_update_removeFieldRecordReferToRemovedField()
    {
        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->patch($uri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(200);
        $fieldThreeRecordEntry = [
            "id" => $this->fieldRecordThree->id,
            "removed" => true,
        ];
        $this->seeInDatabase('TextAreaFieldRecord', $fieldThreeRecordEntry);
    }
}
