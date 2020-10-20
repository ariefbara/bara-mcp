<?php

namespace Tests\Controllers\Client\AsTeamMember\ProgramParticipation;

use Tests\Controllers\RecordPreparation\Shared\ {
    Form\RecordOfMultiSelectField,
    Form\RecordOfSelectField,
    Form\SelectField\RecordOfOption,
    FormRecord\MultiSelectFieldRecord\RecordOfSelectedOption,
    FormRecord\RecordOfMultiSelectFieldRecord
};

class WorksheetController_containMultiSelectFieldTest extends WorksheetTestCase
{
    protected $fieldOne, $fieldTwo_required, $fieldThree_removed;
    protected $optionOneOne, $optionOneTwo, $optionOneThree, $optionTwoOne, $optionTwoTwo_removed, $optionThreeOne;
    protected $fieldRecordOne, $fieldRecordThree;
    protected $selectedOptionOneOne, $selectedOptionOneThree, $selectedOptionThreeOne;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('MultiSelectField')->truncate();
        $this->connection->table('SelectField')->truncate();
        $this->connection->table('T_Option')->truncate();
        $this->connection->table('MultiSelectFieldRecord')->truncate();
        $this->connection->table('SelectedOption')->truncate();
        
        $selectFieldOne = new RecordOfSelectField(1);
        $selectFieldTwo = new RecordOfSelectField(2);
        $selectFieldTwo->mandatory = true;
        $selectFieldThree = new RecordOfSelectField(3);
        
        $this->connection->table('SelectField')->insert($selectFieldOne->toArrayForDbEntry());
        $this->connection->table('SelectField')->insert($selectFieldTwo->toArrayForDbEntry());
        $this->connection->table('SelectField')->insert($selectFieldThree->toArrayForDbEntry());
        
        $this->fieldOne = new RecordOfMultiSelectField($this->form, $selectFieldOne);
        $this->fieldOne->minValue = 2;
        $this->fieldOne->maxValue = 2;
        
        $this->fieldTwo_required = new RecordOfMultiSelectField($this->form, $selectFieldTwo);
        $this->fieldThree_removed = new RecordOfMultiSelectField($this->form, $selectFieldThree);
        $this->fieldThree_removed->removed = true;
        
        $this->connection->table('MultiSelectField')->insert($this->fieldOne->toArrayForDbEntry());
        $this->connection->table('MultiSelectField')->insert($this->fieldTwo_required->toArrayForDbEntry());
        $this->connection->table('MultiSelectField')->insert($this->fieldThree_removed->toArrayForDbEntry());
        
        $this->optionOneOne = new RecordOfOption($selectFieldOne, 11);
        $this->optionOneTwo = new RecordOfOption($selectFieldOne, 12);
        $this->optionOneThree = new RecordOfOption($selectFieldOne, 13);
        $this->optionTwoOne = new RecordOfOption($selectFieldTwo, 21);
        $this->optionTwoTwo_removed = new RecordOfOption($selectFieldTwo, 22);
        $this->optionTwoTwo_removed->removed = true;
        $this->optionThreeOne = new RecordOfOption($selectFieldThree, 31);

        $this->connection->table('T_Option')->insert($this->optionOneOne->toArrayForDbEntry());
        $this->connection->table('T_Option')->insert($this->optionOneTwo->toArrayForDbEntry());
        $this->connection->table('T_Option')->insert($this->optionOneThree->toArrayForDbEntry());
        $this->connection->table('T_Option')->insert($this->optionTwoOne->toArrayForDbEntry());
        $this->connection->table('T_Option')->insert($this->optionTwoTwo_removed->toArrayForDbEntry());
        $this->connection->table('T_Option')->insert($this->optionThreeOne->toArrayForDbEntry());
        
        $this->fieldRecordOne = new RecordOfMultiSelectFieldRecord($this->worksheet->formRecord, $this->fieldOne, 1);
        $this->fieldRecordThree = new RecordOfMultiSelectFieldRecord($this->worksheet->formRecord, $this->fieldThree_removed, 3);
        
        $this->connection->table('MultiSelectFieldRecord')->insert($this->fieldRecordOne->toArrayForDbEntry());
        $this->connection->table('MultiSelectFieldRecord')->insert($this->fieldRecordThree->toArrayForDbEntry());
        
        $this->selectedOptionOneOne = new RecordOfSelectedOption($this->fieldRecordOne, $this->optionOneTwo, 11);
        $this->selectedOptionOneThree = new RecordOfSelectedOption($this->fieldRecordOne, $this->optionOneThree, 13);
        $this->selectedOptionThreeOne = new RecordOfSelectedOption($this->fieldRecordThree, $this->optionThreeOne, 31);
        
        $this->connection->table('SelectedOption')->insert($this->selectedOptionOneOne->toArrayForDbEntry());
        $this->connection->table('SelectedOption')->insert($this->selectedOptionOneThree->toArrayForDbEntry());
        $this->connection->table('SelectedOption')->insert($this->selectedOptionThreeOne->toArrayForDbEntry());
        
        $this->worksheetInput['multiSelectFieldRecords'] = [
            [
                "fieldId" => $this->fieldOne->id,
                "selectedOptionIdList" => [
                    $this->optionOneOne->id,
                    $this->optionOneTwo->id,
                ],
            ],
            [
                "fieldId" => $this->fieldTwo_required->id,
                "selectedOptionIdList" => [
                    $this->optionTwoOne->id,
                ],
            ],
        ];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('MultiSelectField')->truncate();
        $this->connection->table('SelectField')->truncate();
        $this->connection->table('T_Option')->truncate();
        $this->connection->table('MultiSelectFieldRecord')->truncate();
        $this->connection->table('SelectedOption')->truncate();
    }
    public function test_submit()
    {
        $fieldRecordOneResponse = [
            "multiSelectField" => [
                "id" => $this->fieldOne->id,
                "name" => $this->fieldOne->selectField->name,
                "position" => $this->fieldOne->selectField->position,
            ],
        ];
        $selectedOptionOneOneResponse = [
            "id" => $this->worksheetInput['multiSelectFieldRecords'][0]['selectedOptionIdList'][0],
            "name" => $this->optionOneOne->name,
        ];
        $selectedOptionOneTwoResponse = [
            "id" => $this->worksheetInput['multiSelectFieldRecords'][0]['selectedOptionIdList'][1],
            "name" => $this->optionOneTwo->name,
        ];
        $fieldRecordTwoResponse = [
            "multiSelectField" => [
                "id" => $this->fieldTwo_required->id,
                "name" => $this->fieldTwo_required->selectField->name,
                "position" => $this->fieldTwo_required->selectField->position,
            ],
        ];
        $selectedOptionTwoOneResponse = [
            "id" => $this->worksheetInput['multiSelectFieldRecords'][1]['selectedOptionIdList'][0],
            "name" => $this->optionTwoOne->name,
        ];

        $this->post($this->worksheetUri, $this->worksheetInput, $this->teamMember->client->token)
                ->seeStatusCode(201)
                ->seeJsonContains($fieldRecordOneResponse)
                ->seeJsonContains($fieldRecordTwoResponse)
                ->seeJsonContains($selectedOptionOneOneResponse)
                ->seeJsonContains($selectedOptionOneTwoResponse)
                ->seeJsonContains($selectedOptionTwoOneResponse);

        $fieldOneRecordEntry = [
            "MultiSelectField_id" => $this->fieldOne->id,
        ];
        $this->seeInDatabase('MultiSelectFieldRecord', $fieldOneRecordEntry);
        $fieldTwoRecordEntry = [
            "MultiSelectField_id" => $this->fieldTwo_required->id,
        ];
        $this->seeInDatabase('MultiSelectFieldRecord', $fieldTwoRecordEntry);
        $selectedOptionOneOneEntry = [
            "Option_id" => $this->worksheetInput['multiSelectFieldRecords'][0]['selectedOptionIdList'][0],
        ];
        $this->seeInDatabase('SelectedOption', $selectedOptionOneOneEntry);
        $selectedOptionOneTwoEntry = [
            "Option_id" => $this->worksheetInput['multiSelectFieldRecords'][0]['selectedOptionIdList'][1],
        ];
        $this->seeInDatabase('SelectedOption', $selectedOptionOneTwoEntry);
        $selectedOptionTwoOneEntry = [
            "Option_id" => $this->worksheetInput['multiSelectFieldRecords'][1]['selectedOptionIdList'][0],
        ];
        $this->seeInDatabase('SelectedOption', $selectedOptionTwoOneEntry);
    }

    public function test_submit_emptyInputForMandatoryField_error400()
    {
        $this->worksheetInput['multiSelectFieldRecords'][1]['selectedOptionIdList'] = [];
        $this->post($this->worksheetUri, $this->worksheetInput, $this->teamMember->client->token)
                ->seeStatusCode(400);
    }

    public function test_submit_fieldRecordInputOptionNotFound_error404()
    {
        $this->worksheetInput['multiSelectFieldRecords'][1]['selectedOptionIdList'] = [$this->optionTwoTwo_removed->id];
        $this->post($this->worksheetUri, $this->worksheetInput, $this->teamMember->client->token)
                ->seeStatusCode(404);
    }
    public function test_submit_selectedOptionCountLessThanMinValue_error400()
    {
        $this->worksheetInput['multiSelectFieldRecords'][0]['selectedOptionIdList'] = [$this->optionOneOne->id];
        $this->post($this->worksheetUri, $this->worksheetInput, $this->teamMember->client->token)
                ->seeStatusCode(400);
    }
    public function test_submit_selectedOptionCountGreaterThanMaxValue_error400()
    {
        $this->worksheetInput['multiSelectFieldRecords'][0]['selectedOptionIdList'] = [
            $this->optionOneOne->id,
            $this->optionOneTwo->id,
            $this->optionOneThree->id,
        ];
        $this->post($this->worksheetUri, $this->worksheetInput, $this->teamMember->client->token)
                ->seeStatusCode(400);
    }

    public function test_show_includeIntegerFieldRecord()
    {
        $this->worksheet->formRecordResponse['multiSelectFieldRecords'] = [
            [
                "id" => $this->fieldRecordOne->id,
                "multiSelectField" => [
                    "id" => $this->fieldRecordOne->multiSelectField->id,
                    "name" => $this->fieldRecordOne->multiSelectField->selectField->name,
                    "position" => $this->fieldRecordOne->multiSelectField->selectField->position,
                ],
                "selectedOptions" => [
                    [
                        "id" => $this->selectedOptionOneOne->id,
                        "option" => [
                            "id" => $this->selectedOptionOneOne->option->id,
                            "name" => $this->selectedOptionOneOne->option->name,
                        ],
                    ],
                    [
                        "id" => $this->selectedOptionOneThree->id,
                        "option" => [
                            "id" => $this->selectedOptionOneThree->option->id,
                            "name" => $this->selectedOptionOneThree->option->name,
                        ],
                    ],
                ],
            ],
            [
                "id" => $this->fieldRecordThree->id,
                "multiSelectField" => [
                    "id" => $this->fieldRecordThree->multiSelectField->id,
                    "name" => $this->fieldRecordThree->multiSelectField->selectField->name,
                    "position" => $this->fieldRecordThree->multiSelectField->selectField->position,
                ],
                "selectedOptions" => [
                    [
                        "id" => $this->selectedOptionThreeOne->id,
                        "option" => [
                            "id" => $this->selectedOptionThreeOne->option->id,
                            "name" => $this->selectedOptionThreeOne->option->name,
                        ],
                    ],
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
            "multiSelectField" => [
                "id" => $this->fieldOne->id,
                "name" => $this->fieldOne->selectField->name,
                "position" => $this->fieldOne->selectField->position,
            ],
        ];
        $selectedOptionOneOneResponse = [
            "id" => $this->worksheetInput['multiSelectFieldRecords'][0]['selectedOptionIdList'][0],
            "name" => $this->optionOneOne->name,
        ];
        $selectedOptionOneTwoResponse = [
            "id" => $this->worksheetInput['multiSelectFieldRecords'][0]['selectedOptionIdList'][1],
            "name" => $this->optionOneTwo->name,
        ];

        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->patch($uri, $this->worksheetInput, $this->teamMember->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($fieldRecordOneResponse)
                ->seeJsonContains($selectedOptionOneOneResponse)
                ->seeJsonContains($selectedOptionOneTwoResponse);
        
        $fieldOneRecordEntry = [
            "FormRecord_id" => $this->fieldRecordOne->formRecord->id,
            "id" => $this->fieldRecordOne->id,
            "MultiSelectField_id" => $this->fieldOne->id,
        ];
        $this->seeInDatabase('MultiSelectFieldRecord', $fieldOneRecordEntry);
        $selectedOptionOneOneEntry = [
            "MultiSelectFieldRecord_id" => $this->fieldRecordOne->id,
            "Option_id" => $this->worksheetInput['multiSelectFieldRecords'][0]['selectedOptionIdList'][0],
        ];
        $this->seeInDatabase('SelectedOption', $selectedOptionOneOneEntry);
        $selectedOptionOneTwoEntry = [
            "MultiSelectFieldRecord_id" => $this->fieldRecordOne->id,
            "Option_id" => $this->worksheetInput['multiSelectFieldRecords'][0]['selectedOptionIdList'][1],
        ];
        $this->seeInDatabase('SelectedOption', $selectedOptionOneTwoEntry);
    }

    public function test_update_formHasFieldWithNoCorrespondFieldRecordInWorksheet_addAsNewFieldRecord()
    {
        $fieldRecordTwoResponse = [
            "multiSelectField" => [
                "id" => $this->fieldTwo_required->id,
                "name" => $this->fieldTwo_required->selectField->name,
                "position" => $this->fieldTwo_required->selectField->position,
            ],
        ];
        $selectedOptionTwoOneResponse = [
            "id" => $this->worksheetInput['multiSelectFieldRecords'][1]['selectedOptionIdList'][0],
            "name" => $this->optionTwoOne->name,
        ];

        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->patch($uri, $this->worksheetInput, $this->teamMember->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($fieldRecordTwoResponse)
                ->seeJsonContains($selectedOptionTwoOneResponse);

        $fieldTwoRecordEntry = [
            "FormRecord_id" => $this->fieldRecordOne->formRecord->id,
            "MultiSelectField_id" => $this->fieldTwo_required->id,
        ];
        $this->seeInDatabase('MultiSelectFieldRecord', $fieldTwoRecordEntry);
        $selectedOptionTwoOneEntry = [
            "Option_id" => $this->worksheetInput['multiSelectFieldRecords'][1]['selectedOptionIdList'][0],
        ];
        $this->seeInDatabase('SelectedOption', $selectedOptionTwoOneEntry);
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
        $this->seeInDatabase('MultiSelectFieldRecord', $fieldThreeRecordEntry);
    }
    public function test_update_removeExistingSelectedOptionNoLongerSelected()
    {
        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->patch($uri, $this->worksheetInput, $this->teamMember->client->token)
                ->seeStatusCode(200);
        $selectedOptionOneThreeEntry = [
            "id" => $this->selectedOptionOneThree->id,
            "removed" => true,
        ];
        $this->seeInDatabase("SelectedOption", $selectedOptionOneThreeEntry);
    }
}
