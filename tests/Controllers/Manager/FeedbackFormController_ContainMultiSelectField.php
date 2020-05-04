<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\RecordPreparation\Shared\Form\ {
    RecordOfMultiSelectField,
    RecordOfSelectField
};

class FeedbackFormController_ContainMultiSelectField extends FeedbackFormTestCase
{
    protected $multiSelectField;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $selectField = new RecordOfSelectField(0);
        $this->connection->table('SelectField')->insert($selectField->toArrayForDbEntry());
        
        $this->multiSelectField = new RecordOfMultiSelectField($this->feedbackForm->form, $selectField);
        $this->connection->table('MultiSelectField')->insert($this->multiSelectField->toArrayForDbEntry());
        
        $this->feedbackFormInput['multiSelectFields'][] = [
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
            "name" => $this->feedbackFormInput['multiSelectFields'][0]['name'],
            "description" => $this->feedbackFormInput['multiSelectFields'][0]['description'],
            "position" => $this->feedbackFormInput['multiSelectFields'][0]['position'],
            "mandatory" => $this->feedbackFormInput['multiSelectFields'][0]['mandatory'],
            "minValue" => $this->feedbackFormInput['multiSelectFields'][0]['minValue'],
            "maxValue" => $this->feedbackFormInput['multiSelectFields'][0]['maxValue'],
            "options" => [],
        ];
        
        $this->post($this->feedbackFormUri, $this->feedbackFormInput, $this->manager->token)
            ->seeStatusCode(201)
            ->seeJsonContains($response);
        
        $selectFieldEntry = [
            "name" => $this->feedbackFormInput['multiSelectFields'][0]['name'],
            "description" => $this->feedbackFormInput['multiSelectFields'][0]['description'],
            "position" => $this->feedbackFormInput['multiSelectFields'][0]['position'],
            "mandatory" => $this->feedbackFormInput['multiSelectFields'][0]['mandatory'],
        ];
        $this->seeInDatabase("SelectField", $selectFieldEntry);
        
        $multiSelectFieldEntry = [
            "minimumValue" => $this->feedbackFormInput['multiSelectFields'][0]['minValue'],
            "maximumValue" => $this->feedbackFormInput['multiSelectFields'][0]['maxValue'],
            "removed" => false,
        ];
        $this->seeInDatabase('MultiSelectField', $multiSelectFieldEntry);
    }
    public function test_update_fieldInputWithoutId_addAsNewField()
    {
        $response = [
            "name" => $this->feedbackFormInput['multiSelectFields'][0]['name'],
            "description" => $this->feedbackFormInput['multiSelectFields'][0]['description'],
            "position" => $this->feedbackFormInput['multiSelectFields'][0]['position'],
            "mandatory" => $this->feedbackFormInput['multiSelectFields'][0]['mandatory'],
            "minValue" => $this->feedbackFormInput['multiSelectFields'][0]['minValue'],
            "maxValue" => $this->feedbackFormInput['multiSelectFields'][0]['maxValue'],
            "options" => [],
        ];
        
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->patch($uri, $this->feedbackFormInput, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
        
        $selectFieldEntry = [
            "name" => $this->feedbackFormInput['multiSelectFields'][0]['name'],
            "description" => $this->feedbackFormInput['multiSelectFields'][0]['description'],
            "position" => $this->feedbackFormInput['multiSelectFields'][0]['position'],
            "mandatory" => $this->feedbackFormInput['multiSelectFields'][0]['mandatory'],
        ];
        $this->seeInDatabase("SelectField", $selectFieldEntry);
        
        $multiSelectFieldEntry = [
            "minimumValue" => $this->feedbackFormInput['multiSelectFields'][0]['minValue'],
            "maximumValue" => $this->feedbackFormInput['multiSelectFields'][0]['maxValue'],
            "removed" => false,
        ];
        $this->seeInDatabase('MultiSelectField', $multiSelectFieldEntry);
    }
    public function test_update_fieldInputWithId_updateExistingFieldWithSameId()
    {
        $this->feedbackFormInput['multiSelectFields'][0]['id'] = $this->multiSelectField->id;
        $response = [
            "id" => $this->feedbackForm->id,
            "name" => $this->feedbackFormInput['name'],
            "description" => $this->feedbackFormInput['description'],
            "multiSelectFields" => [
                [
                    "id" => $this->multiSelectField->id,
                    "name" => $this->feedbackFormInput['multiSelectFields'][0]['name'],
                    "description" => $this->feedbackFormInput['multiSelectFields'][0]['description'],
                    "position" => $this->feedbackFormInput['multiSelectFields'][0]['position'],
                    "mandatory" => $this->feedbackFormInput['multiSelectFields'][0]['mandatory'],
                    "minValue" => $this->feedbackFormInput['multiSelectFields'][0]['minValue'],
                    "maxValue" => $this->feedbackFormInput['multiSelectFields'][0]['maxValue'],
                    "options" => [],
                ],
            ],
        ];
        
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->patch($uri, $this->feedbackFormInput, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
        
        $selectFieldEntry = [
            "id" => $this->multiSelectField->selectField->id,
            "name" => $this->feedbackFormInput['multiSelectFields'][0]['name'],
            "description" => $this->feedbackFormInput['multiSelectFields'][0]['description'],
            "position" => $this->feedbackFormInput['multiSelectFields'][0]['position'],
            "mandatory" => $this->feedbackFormInput['multiSelectFields'][0]['mandatory'],
        ];
        $this->seeInDatabase("SelectField", $selectFieldEntry);
        
        $multiSelectFieldEntry = [
            "Form_id" => $this->feedbackForm->id,
            "id" => $this->multiSelectField->id,
            "minimumValue" => $this->feedbackFormInput['multiSelectFields'][0]['minValue'],
            "maximumValue" => $this->feedbackFormInput['multiSelectFields'][0]['maxValue'],
            "removed" => false,
        ];
        $this->seeInDatabase('MultiSelectField', $multiSelectFieldEntry);
    }
    public function test_update_existingFieldNotMentionInUpdateInput_removeThisField()
    {
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->patch($uri, $this->feedbackFormInput, $this->manager->token)
            ->seeStatusCode(200);
        
        $multiSelectFieldEntry = [
            "Form_id" => $this->feedbackForm->id,
            "id" => $this->multiSelectField->id,
            "removed" => true,
        ];
        $this->seeInDatabase('MultiSelectField', $multiSelectFieldEntry);
    }
    public function test_update_fieldInputWithIdNotFoundIndRecord_addAsNewField()
    {
        $this->feedbackFormInput['multiSelectFields'][0]['id'] = 'not existing id';
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->patch($uri, $this->feedbackFormInput, $this->manager->token)
            ->seeStatusCode(200);
        
        $removedMultiSelectField = [
            "Form_id" => $this->feedbackForm->id,
            "id" => $this->multiSelectField->id,
            "removed" => true,
        ];
        $this->seeInDatabase('MultiSelectField', $removedMultiSelectField);
        
        $selectFieldEntry = [
            "name" => $this->feedbackFormInput['multiSelectFields'][0]['name'],
            "description" => $this->feedbackFormInput['multiSelectFields'][0]['description'],
            "position" => $this->feedbackFormInput['multiSelectFields'][0]['position'],
            "mandatory" => $this->feedbackFormInput['multiSelectFields'][0]['mandatory'],
        ];
        $this->seeInDatabase("SelectField", $selectFieldEntry);
        
        $multiSelectFieldEntry = [
            "minimumValue" => $this->feedbackFormInput['multiSelectFields'][0]['minValue'],
            "maximumValue" => $this->feedbackFormInput['multiSelectFields'][0]['maxValue'],
            "removed" => false,
        ];
        $this->seeInDatabase('MultiSelectField', $multiSelectFieldEntry);
    }
    public function test_show()
    {
        $response = [
            "id" => $this->feedbackForm->id,
            "name" => $this->feedbackForm->form->name,
            "description" => $this->feedbackForm->form->description,
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
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->get($uri, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
}
