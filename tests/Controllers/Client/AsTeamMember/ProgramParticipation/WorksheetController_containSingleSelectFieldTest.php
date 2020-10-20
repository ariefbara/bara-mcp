<?php

namespace Tests\Controllers\Client\AsTeamMember\ProgramParticipation;

use Tests\Controllers\RecordPreparation\Shared\ {
    Form\RecordOfSelectField,
    Form\RecordOfSingleSelectField,
    Form\SelectField\RecordOfOption,
    FormRecord\RecordOfSingleSelectFieldRecord
};

class WorksheetController_containSingleSelectFieldTest extends WorksheetTestCase
{

    protected $fieldOne, $fieldTwo_required, $fieldThree_removed;
    protected $optionOneOne, $optionOneTwo_removed, $optionTwoOne, $optionTwoTwo, $optionThreeOne;
    protected $fieldRecordOne, $fieldRecordThree;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('SingleSelectField')->truncate();
        $this->connection->table('SelectField')->truncate();
        $this->connection->table('T_Option')->truncate();
        $this->connection->table('SingleSelectFieldRecord')->truncate();
        
        $selectFieldOne = new RecordOfSelectField(1);
        $selectFieldTwo = new RecordOfSelectField(2);
        $selectFieldTwo->mandatory = true;
        $selectFieldThree = new RecordOfSelectField(3);
        
        $this->connection->table('SelectField')->truncate();
        $this->connection->table('SelectField')->insert($selectFieldOne->toArrayForDbEntry());
        $this->connection->table('SelectField')->insert($selectFieldTwo->toArrayForDbEntry());
        $this->connection->table('SelectField')->insert($selectFieldThree->toArrayForDbEntry());

        $this->fieldOne = new RecordOfSingleSelectField($this->form, $selectFieldOne);
        $this->fieldTwo_required = new RecordOfSingleSelectField($this->form, $selectFieldTwo);
        $this->fieldThree_removed = new RecordOfSingleSelectField($this->form, $selectFieldThree);
        $this->fieldThree_removed->removed = true;
        
        $this->connection->table('SingleSelectField')->insert($this->fieldOne->toArrayForDbEntry());
        $this->connection->table('SingleSelectField')->insert($this->fieldTwo_required->toArrayForDbEntry());
        $this->connection->table('SingleSelectField')->insert($this->fieldThree_removed->toArrayForDbEntry());

        $this->optionOneOne = new RecordOfOption($selectFieldOne, 11);
        $this->optionOneTwo_removed = new RecordOfOption($selectFieldOne, 12);
        $this->optionOneTwo_removed->removed = true;

        $this->optionTwoOne = new RecordOfOption($selectFieldTwo, 21);
        $this->optionTwoTwo = new RecordOfOption($selectFieldTwo, 22);
        $this->optionThreeOne = new RecordOfOption($selectFieldThree, 31);

        $this->connection->table('T_Option')->truncate();
        $this->connection->table('T_Option')->insert($this->optionOneOne->toArrayForDbEntry());
        $this->connection->table('T_Option')->insert($this->optionOneTwo_removed->toArrayForDbEntry());
        $this->connection->table('T_Option')->insert($this->optionTwoOne->toArrayForDbEntry());
        $this->connection->table('T_Option')->insert($this->optionTwoTwo->toArrayForDbEntry());
        $this->connection->table('T_Option')->insert($this->optionThreeOne->toArrayForDbEntry());

        $this->fieldRecordOne = new RecordOfSingleSelectFieldRecord($this->worksheet->formRecord, $this->fieldOne, $this->optionOneTwo_removed, 1);
        $this->fieldRecordThree = new RecordOfSingleSelectFieldRecord($this->worksheet->formRecord, $this->fieldThree_removed, $this->optionThreeOne, 3);
        $this->connection->table('SingleSelectFieldRecord')->insert($this->fieldRecordOne->toArrayForDbEntry());
        $this->connection->table('SingleSelectFieldRecord')->insert($this->fieldRecordThree->toArrayForDbEntry());

        $this->worksheetInput['singleSelectFieldRecords'] = [
            [
                "fieldId" => $this->fieldOne->id,
                "selectedOptionId" => $this->optionOneOne->id,
            ],
            [
                "fieldId" => $this->fieldTwo_required->id,
                "selectedOptionId" => $this->optionTwoOne->id,
            ],
        ];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('SingleSelectField')->truncate();
        $this->connection->table('SelectField')->truncate();
        $this->connection->table('T_Option')->truncate();
        $this->connection->table('SingleSelectFieldRecord')->truncate();
    }
    public function test_submit()
    {
        $fieldRecordOneResponse = [
            "singleSelectField" => [
                "id" => $this->fieldOne->id,
                "name" => $this->fieldOne->selectField->name,
                "position" => $this->fieldOne->selectField->position,
            ],
            "selectedOption" => [
                "id" => $this->worksheetInput['singleSelectFieldRecords'][0]['selectedOptionId'],
                "name" => $this->optionOneOne->name,
            ],
        ];
        $fieldRecordTwoResponse = [
            "singleSelectField" => [
                "id" => $this->fieldTwo_required->id,
                "name" => $this->fieldTwo_required->selectField->name,
                "position" => $this->fieldTwo_required->selectField->position,
            ],
            "selectedOption" => [
                "id" => $this->worksheetInput['singleSelectFieldRecords'][1]['selectedOptionId'],
                "name" => $this->optionTwoOne->name,
            ],
        ];

        $this->post($this->worksheetUri, $this->worksheetInput, $this->teamMember->client->token)
                ->seeStatusCode(201)
                ->seeJsonContains($fieldRecordOneResponse)
                ->seeJsonContains($fieldRecordTwoResponse);

        $fieldOneRecordEntry = [
            "SingleSelectField_id" => $this->fieldOne->id,
            "Option_id" => $this->worksheetInput['singleSelectFieldRecords'][0]['selectedOptionId'],
        ];
        $this->seeInDatabase('SingleSelectFieldRecord', $fieldOneRecordEntry);
        $fieldTwoRecordEntry = [
            "SingleSelectField_id" => $this->fieldTwo_required->id,
            "Option_id" => $this->worksheetInput['singleSelectFieldRecords'][1]['selectedOptionId'],
        ];
        $this->seeInDatabase('SingleSelectFieldRecord', $fieldTwoRecordEntry);
    }

    public function test_submit_emptyInputForMandatoryField_error400()
    {
        $this->worksheetInput['singleSelectFieldRecords'][1]['selectedOptionId'] = '';
        $this->post($this->worksheetUri, $this->worksheetInput, $this->teamMember->client->token)
                ->seeStatusCode(400);
    }

    public function test_submit_fieldRecordInputOptionNotFound_error404()
    {
        $this->worksheetInput['singleSelectFieldRecords'][0]['selectedOptionId'] = $this->optionOneTwo_removed->id;
        $this->post($this->worksheetUri, $this->worksheetInput, $this->teamMember->client->token)
                ->seeStatusCode(404);
    }

    public function test_submit_emptyFieldRecordInputForNonMandatoryField_setSelectedOptionNull()
    {
        $this->worksheetInput['singleSelectFieldRecords'][0]['selectedOptionId'] = '';
        $this->post($this->worksheetUri, $this->worksheetInput, $this->teamMember->client->token)
                ->seeStatusCode(201);

        $fieldOneRecordEntry = [
            "SingleSelectField_id" => $this->fieldOne->id,
            "Option_id" => null,
        ];
        $this->seeInDatabase('SingleSelectFieldRecord', $fieldOneRecordEntry);
    }

    public function test_show()
    {
        $this->worksheet->formRecordResponse['singleSelectFieldRecords'] = [
            [
                "id" => $this->fieldRecordOne->id,
                "singleSelectField" => [
                    "id" => $this->fieldRecordOne->singleSelectField->id,
                    "name" => $this->fieldRecordOne->singleSelectField->selectField->name,
                    "position" => $this->fieldRecordOne->singleSelectField->selectField->position,
                ],
                "selectedOption" => [
                    "id" => $this->fieldRecordOne->option->id,
                    "name" => $this->fieldRecordOne->option->name,
                ],
            ],
            [
                "id" => $this->fieldRecordThree->id,
                "singleSelectField" => [
                    "id" => $this->fieldRecordThree->singleSelectField->id,
                    "name" => $this->fieldRecordThree->singleSelectField->selectField->name,
                    "position" => $this->fieldRecordThree->singleSelectField->selectField->position,
                ],
                "selectedOption" => [
                    "id" => $this->fieldRecordThree->option->id,
                    "name" => $this->fieldRecordThree->option->name,
                ],
            ],
        ];
        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->get($uri, $this->teamMember->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($this->worksheet->formRecordResponse);
    }
    
    public function test_update_updateExistingFieldRecord()
    {
        $fieldRecordOneResponse = [
            "id" => $this->fieldRecordOne->id,
            "singleSelectField" => [
                "id" => $this->fieldOne->id,
                "name" => $this->fieldOne->selectField->name,
                "position" => $this->fieldOne->selectField->position,
            ],
            "selectedOption" => [
                "id" => $this->worksheetInput['singleSelectFieldRecords'][0]['selectedOptionId'],
                "name" => $this->optionOneOne->name,
            ],
        ];

        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->patch($uri, $this->worksheetInput, $this->teamMember->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($fieldRecordOneResponse);

        $fieldOneRecordEntry = [
            "FormRecord_id" => $this->fieldRecordOne->formRecord->id,
            "id" => $this->fieldRecordOne->id,
            "SingleSelectField_id" => $this->fieldOne->id,
            "Option_id" => $this->worksheetInput['singleSelectFieldRecords'][0]['selectedOptionId'],
        ];
        $this->seeInDatabase('SingleSelectFieldRecord', $fieldOneRecordEntry);
    }

    public function test_update_formHasFieldWithNoCorrespondToAnyFieldRecordInWorksheet_addAsNewFieldRecord()
    {
        $fieldRecordTwoResponse = [
            "singleSelectField" => [
                "id" => $this->fieldTwo_required->id,
                "name" => $this->fieldTwo_required->selectField->name,
                "position" => $this->fieldTwo_required->selectField->position,
            ],
            "selectedOption" => [
                "id" => $this->worksheetInput['singleSelectFieldRecords'][1]['selectedOptionId'],
                "name" => $this->optionTwoOne->name,
            ],
        ];

        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->patch($uri, $this->worksheetInput, $this->teamMember->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($fieldRecordTwoResponse);

        $fieldTwoRecordEntry = [
            "SingleSelectField_id" => $this->fieldTwo_required->id,
            "Option_id" => $this->worksheetInput['singleSelectFieldRecords'][1]['selectedOptionId'],
        ];
        $this->seeInDatabase('SingleSelectFieldRecord', $fieldTwoRecordEntry);
    }

    public function test_update_removeFieldRecordReferToRemovedField()
    {
        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->patch($uri, $this->worksheetInput, $this->teamMember->client->token)
                ->seeStatusCode(200);
        $fieldThreeRecordEntry = [
            "id" => $this->fieldRecordThree->id,
            "removed" => true,
        ];
        $this->seeInDatabase('SingleSelectFieldRecord', $fieldThreeRecordEntry);
    }

}
