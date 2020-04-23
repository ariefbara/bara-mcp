<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\RecordPreparation\Shared\Form\ {
    RecordOfMultiSelectField,
    RecordOfSelectField,
    RecordOfSingleSelectField,
    SelectField\RecordOfOption
};

class ConsultationFeedbackFormController_ContainOptionOfSelectFieldTest extends ConsultationFeedbackFormTestCase
{
    protected $selectField;
    protected $option;
    protected $singleSelectField;
    protected $multiSelectField;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationFeedbackFormInput['singleSelectFields'][] = [
            "name" => "new single select field name",
                "description" => "new single select field description",
                "position" => "new single select field position",
                "mandatory" => true,
                "defaultValue" => "new single select field default value",
                "options" => [
                    [
                        "name" => "new single select field option name",
                        "description" => "new single select field option description",
                        "position" => "new single select field option position",
                    ],
                ],
        ];
        $this->consultationFeedbackFormInput['multiSelectFields'][] = [
            "name" => "new multi select field name",
                "description" => "new multi select field description",
                "position" => "new multi select field position",
                "mandatory" => true,
                "minValue" => 2,
                "maxValue" => 4,
                "options" => [
                    [
                        "name" => "new multi select field option name",
                        "description" => "new multi select field option description",
                        "position" => "new multi select field option position",
                    ],
                ],
        ];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    protected function setSelectField()
    {
        $this->selectField = new RecordOfSelectField(0);
        $this->connection->table('SelectField')->insert($this->selectField->toArrayForDbEntry());
        
        $this->option = new RecordOfOption($this->selectField, 0);
        $this->connection->table('T_Option')->insert($this->option->toArrayForDbEntry());
    }
    protected function setSingleSelectField()
    {
        $this->setSelectField();
        
        $this->singleSelectField = new RecordOfSingleSelectField($this->consultationFeedbackForm->form, $this->selectField);
        $this->connection->table('SingleSelectField')->insert($this->singleSelectField->toArrayForDbEntry());
    }
    protected function setMultiSelectField()
    {
        $this->setSelectField();
        
        $this->multiSelectField = new RecordOfMultiSelectField($this->consultationFeedbackForm->form, $this->selectField);
        $this->connection->table("MultiSelectField")->insert($this->multiSelectField->toArrayForDbEntry());
    }
    
    public function test_add()
    {
$this->disableExceptionHandling();
        $singleSelectFieldOptionResponse = [
            "name" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['options'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['options'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['options'][0]['position'],
        ];
        $multiSelectFieldOptionResponse = [
            "name" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['options'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['options'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['options'][0]['position'],
        ];
        
        $this->post($this->consultationFeedbackFormUri, $this->consultationFeedbackFormInput, $this->manager->token)
            ->seeStatusCode(201)
            ->seeJsonContains($singleSelectFieldOptionResponse)
            ->seeJsonContains($multiSelectFieldOptionResponse);
        
        $optionEntryForSingleSelectField = [
            "name" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['options'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['options'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['options'][0]['position'],
            "removed" => false,
        ];
        $this->seeInDatabase("T_Option", $optionEntryForSingleSelectField);
        $optionEntryForMultiSelectField = [
            "name" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['options'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['options'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['options'][0]['position'],
            "removed" => false,
        ];
        $this->seeInDatabase("T_Option", $optionEntryForMultiSelectField);
    }
    
    protected function executeUpdateForSingleSelectField()
    {
        $response = [
            "name" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['options'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['options'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['options'][0]['position'],
        ];
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->patch($uri, $this->consultationFeedbackFormInput, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
    public function test_updateFormSingleSelectField_singleFieldInputOptionWithoutId_addAsNewOption()
    {
        $this->setSingleSelectField();
        $this->consultationFeedbackFormInput['singleSelectFields'][0]['id'] = $this->singleSelectField->id;
        $this->executeUpdateForSingleSelectField();
        
        $optionEntry = [
            "name" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['options'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['options'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['options'][0]['position'],
            "removed" => false,
        ];
        $this->seeInDatabase("T_Option", $optionEntry);
    }
    public function test_updateFormSingleSelectField_singleFieldInputContainNonUpdatedOption_removeOption()
    {
        $this->setSingleSelectField();
        $this->consultationFeedbackFormInput['singleSelectFields'][0]['id'] = $this->singleSelectField->id;
        $this->executeUpdateForSingleSelectField();
        
        $optionEntry = [
            "SelectField_id" => $this->singleSelectField->selectField->id,
            "id" => $this->option->id,
            "removed" => true,
        ];
        $this->seeInDatabase("T_Option", $optionEntry);
    }
    public function test_updateFormSingleSelectField_optionInputContainIdIndicateUpdate_updateOption()
    {
        $this->setSingleSelectField();
        $this->consultationFeedbackFormInput['singleSelectFields'][0]['id'] = $this->singleSelectField->id;
        $this->consultationFeedbackFormInput['singleSelectFields'][0]['options'][0]['id'] = $this->option->id;
        $this->executeUpdateForSingleSelectField();
        
        $optionEntry = [
            "SelectField_id" => $this->singleSelectField->selectField->id,
            "id" => $this->option->id,
            "name" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['options'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['options'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['options'][0]['position'],
            "removed" => false,
        ];
        $this->seeInDatabase("T_Option", $optionEntry);
    }
    public function test_updateFormSingleSelectField_optionInputContainNonExistingId_addAsNewOption()
    {
        $this->setSingleSelectField();
        $this->consultationFeedbackFormInput['singleSelectFields'][0]['id'] = $this->singleSelectField->id;
        $this->consultationFeedbackFormInput['singleSelectFields'][0]['options'][0]['id'] = 'non existing option-id';
        $this->executeUpdateForSingleSelectField();
        
        $removedOptionEntry = [
            "SelectField_id" => $this->singleSelectField->selectField->id,
            "id" => $this->option->id,
            "removed" => true,
        ];
        $this->seeInDatabase("T_Option", $removedOptionEntry);
        $newOptionEntry = [
            "SelectField_id" => $this->singleSelectField->selectField->id,
            "name" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['options'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['options'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['options'][0]['position'],
            "removed" => false,
        ];
        $this->seeInDatabase("T_Option", $newOptionEntry);
    }
    
    protected function executeUpdateForMultiSelectField()
    {
        $response = [
            "name" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['options'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['options'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['options'][0]['position'],
        ];
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->patch($uri, $this->consultationFeedbackFormInput, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
    public function test_updateForMultiSelectField_optionInputWithoutId_addAsNewOption()
    {
        $this->setMultiSelectField();
        $this->consultationFeedbackFormInput['multiSelectFields'][0]['id'] = $this->multiSelectField->id;
        $this->executeUpdateForMultiSelectField();
        
        $optionEntry = [
            "name" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['options'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['options'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['options'][0]['position'],
            "removed" => false,
        ];
        $this->seeInDatabase("T_Option", $optionEntry);
    }
    public function test_updateForMultiSelectField_multiSelectFieldContainNonUpdatedOption_removeOption()
    {
        $this->setMultiSelectField();
        $this->consultationFeedbackFormInput['multiSelectFields'][0]['id'] = $this->multiSelectField->id;
        $this->executeUpdateForMultiSelectField();
        
        $optionEntry = [
            "SelectField_id" => $this->multiSelectField->selectField->id,
            "id" => $this->option->id,
            "removed" => true,
        ];
        $this->seeInDatabase("T_Option", $optionEntry);
    }
    public function test_updateForMultiSelectField_optionInputContainIdIndicateUpdate_updateOption()
    {
        $this->setMultiSelectField();
        $this->consultationFeedbackFormInput['multiSelectFields'][0]['id'] = $this->multiSelectField->id;
        $this->consultationFeedbackFormInput['multiSelectFields'][0]['options'][0]['id'] = $this->option->id;
        $this->executeUpdateForMultiSelectField();
        
        $optionEntry = [
            "SelectField_id" => $this->multiSelectField->selectField->id,
            "id" => $this->option->id,
            "name" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['options'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['options'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['options'][0]['position'],
            "removed" => false,
        ];
        $this->seeInDatabase("T_Option", $optionEntry);
    }
    public function test_updateForMultiSelectField_optionInputContainNonExistingId_addAsNewOption()
    {
        $this->setMultiSelectField();
        $this->consultationFeedbackFormInput['multiSelectFields'][0]['id'] = $this->multiSelectField->id;
        $this->consultationFeedbackFormInput['multiSelectFields'][0]['options'][0]['id'] = 'non existing option-id';
        $this->executeUpdateForMultiSelectField();
        
        $removedOptionEntry = [
            "SelectField_id" => $this->multiSelectField->selectField->id,
            "id" => $this->option->id,
            "removed" => true,
        ];
        $this->seeInDatabase("T_Option", $removedOptionEntry);
        $newOptionEntry = [
            "SelectField_id" => $this->multiSelectField->selectField->id,
            "name" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['options'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['options'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['options'][0]['position'],
            "removed" => false,
        ];
        $this->seeInDatabase("T_Option", $newOptionEntry);
    }
    
    public function test_showForSingleSelectField()
    {
        $this->setSingleSelectField();
        $response = [
            "id" => $this->consultationFeedbackForm->id,
            "name" => $this->consultationFeedbackForm->form->name,
            "description" => $this->consultationFeedbackForm->form->description,
            "stringFields" => [],
            "integerFields" => [],
            "textAreaFields" => [],
            "attachmentFields" => [],
            "singleSelectFields" => [
                [
                    "id" => $this->singleSelectField->id,
                    "name" => $this->singleSelectField->selectField->name,
                    "description" => $this->singleSelectField->selectField->description,
                    "position" => $this->singleSelectField->selectField->position,
                    "mandatory" => $this->singleSelectField->selectField->mandatory,
                    "defaultValue" => $this->singleSelectField->defaultValue,
                    "options" => [
                        [
                            "id" => $this->option->id,
                            "name" => $this->option->name,
                            "description" => $this->option->description,
                            "position" => $this->option->position,
                        ],
                    ],
                ],
            ],
            "multiSelectFields" => [],
        ];
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->get($uri, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
    public function test_showForMultiSelectField()
    {
        $this->setMultiSelectField();
        $response = [
            "id" => $this->consultationFeedbackForm->id,
            "name" => $this->consultationFeedbackForm->form->name,
            "description" => $this->consultationFeedbackForm->form->description,
            "stringFields" => [],
            "integerFields" => [],
            "textAreaFields" => [],
            "attachmentFields" => [],
            "singleSelectFields" => [],
            "multiSelectFields" => [
                [
                    "id" => $this->multiSelectField->id,
                    "name" => $this->multiSelectField->selectField->name,
                    "description" => $this->multiSelectField->selectField->description,
                    "position" => $this->multiSelectField->selectField->position,
                    "mandatory" => $this->multiSelectField->selectField->mandatory,
                    "minValue" => $this->multiSelectField->minValue,
                    "maxValue" => $this->multiSelectField->maxValue,
                    "options" => [
                        [
                            "id" => $this->option->id,
                            "name" => $this->option->name,
                            "description" => $this->option->description,
                            "position" => $this->option->position,
                        ],
                    ],
                ],
            ],
        ];
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->get($uri, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
}
