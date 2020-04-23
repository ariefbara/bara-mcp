<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\RecordPreparation\Shared\Form\ {
    RecordOfMultiSelectField,
    RecordOfSelectField
};

class ConsultationFeedbackFormController_ContainMultiSelectField extends ConsultationFeedbackFormTestCase
{
    protected $multiSelectField;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $selectField = new RecordOfSelectField(0);
        $this->connection->table('SelectField')->insert($selectField->toArrayForDbEntry());
        
        $this->multiSelectField = new RecordOfMultiSelectField($this->consultationFeedbackForm->form, $selectField);
        $this->connection->table('MultiSelectField')->insert($this->multiSelectField->toArrayForDbEntry());
        
        $this->consultationFeedbackFormInput['multiSelectFields'][] = [
            "name" => "new single select field name",
            "description" => "new single select field description",
            "position" => "new single select field position",
            "mandatory" => true,
            "minValue" => 1,
            "maxValue" => 4,
            "options" => [],
        ];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    public function test_add()
    {
        $response = [
            "name" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['mandatory'],
            "minValue" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['minValue'],
            "maxValue" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['maxValue'],
            "options" => [],
        ];
        
        $this->post($this->consultationFeedbackFormUri, $this->consultationFeedbackFormInput, $this->manager->token)
            ->seeStatusCode(201)
            ->seeJsonContains($response);
        
        $selectFieldEntry = [
            "name" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['mandatory'],
        ];
        $this->seeInDatabase("SelectField", $selectFieldEntry);
        
        $multiSelectFieldEntry = [
            "minimumValue" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['minValue'],
            "maximumValue" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['maxValue'],
            "removed" => false,
        ];
        $this->seeInDatabase('MultiSelectField', $multiSelectFieldEntry);
    }
    public function test_update_fieldInputWithoutId_addAsNewField()
    {
        $response = [
            "name" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['mandatory'],
            "minValue" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['minValue'],
            "maxValue" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['maxValue'],
            "options" => [],
        ];
        
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->patch($uri, $this->consultationFeedbackFormInput, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
        
        $selectFieldEntry = [
            "name" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['mandatory'],
        ];
        $this->seeInDatabase("SelectField", $selectFieldEntry);
        
        $multiSelectFieldEntry = [
            "minimumValue" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['minValue'],
            "maximumValue" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['maxValue'],
            "removed" => false,
        ];
        $this->seeInDatabase('MultiSelectField', $multiSelectFieldEntry);
    }
    public function test_update_fieldInputWithId_updateExistingFieldWithSameId()
    {
        $this->consultationFeedbackFormInput['multiSelectFields'][0]['id'] = $this->multiSelectField->id;
        $response = [
            "id" => $this->consultationFeedbackForm->id,
            "name" => $this->consultationFeedbackFormInput['name'],
            "description" => $this->consultationFeedbackFormInput['description'],
            "multiSelectFields" => [
                [
                    "id" => $this->multiSelectField->id,
                    "name" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['name'],
                    "description" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['description'],
                    "position" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['position'],
                    "mandatory" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['mandatory'],
                    "minValue" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['minValue'],
                    "maxValue" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['maxValue'],
                    "options" => [],
                ],
            ],
        ];
        
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->patch($uri, $this->consultationFeedbackFormInput, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
        
        $selectFieldEntry = [
            "id" => $this->multiSelectField->selectField->id,
            "name" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['mandatory'],
        ];
        $this->seeInDatabase("SelectField", $selectFieldEntry);
        
        $multiSelectFieldEntry = [
            "Form_id" => $this->consultationFeedbackForm->id,
            "id" => $this->multiSelectField->id,
            "minimumValue" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['minValue'],
            "maximumValue" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['maxValue'],
            "removed" => false,
        ];
        $this->seeInDatabase('MultiSelectField', $multiSelectFieldEntry);
    }
    public function test_update_existingFieldNotMentionInUpdateInput_removeThisField()
    {
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->patch($uri, $this->consultationFeedbackFormInput, $this->manager->token)
            ->seeStatusCode(200);
        
        $multiSelectFieldEntry = [
            "Form_id" => $this->consultationFeedbackForm->id,
            "id" => $this->multiSelectField->id,
            "removed" => true,
        ];
        $this->seeInDatabase('MultiSelectField', $multiSelectFieldEntry);
    }
    public function test_update_fieldInputWithIdNotFoundIndRecord_addAsNewField()
    {
        $this->consultationFeedbackFormInput['multiSelectFields'][0]['id'] = 'not existing id';
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->patch($uri, $this->consultationFeedbackFormInput, $this->manager->token)
            ->seeStatusCode(200);
        
        $removedMultiSelectField = [
            "Form_id" => $this->consultationFeedbackForm->id,
            "id" => $this->multiSelectField->id,
            "removed" => true,
        ];
        $this->seeInDatabase('MultiSelectField', $removedMultiSelectField);
        
        $selectFieldEntry = [
            "name" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['mandatory'],
        ];
        $this->seeInDatabase("SelectField", $selectFieldEntry);
        
        $multiSelectFieldEntry = [
            "minimumValue" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['minValue'],
            "maximumValue" => $this->consultationFeedbackFormInput['multiSelectFields'][0]['maxValue'],
            "removed" => false,
        ];
        $this->seeInDatabase('MultiSelectField', $multiSelectFieldEntry);
    }
    public function test_show()
    {
        $response = [
            "id" => $this->consultationFeedbackForm->id,
            "name" => $this->consultationFeedbackForm->form->name,
            "description" => $this->consultationFeedbackForm->form->description,
            "multiSelectFields" => [
                [
                    "id" => $this->multiSelectField->id,
                    "name" => $this->multiSelectField->selectField->name,
                    "description" => $this->multiSelectField->selectField->description,
                    "position" => $this->multiSelectField->selectField->position,
                    "mandatory" => $this->multiSelectField->selectField->mandatory,
                    "minValue" => $this->multiSelectField->minValue,
                    "maxValue" => $this->multiSelectField->maxValue,
                    "options" => [],
                ],
            ],
        ];
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->get($uri, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
}
