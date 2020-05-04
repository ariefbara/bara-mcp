<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfIntegerField;

class FeedbackFormController_ContainIntegerFieldTest extends FeedbackFormTestCase
{
    protected $integerField;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->integerField = new RecordOfIntegerField($this->feedbackForm->form, 0);
        $this->connection->table('IntegerField')->insert($this->integerField->toArrayForDbEntry());
        
        $this->feedbackFormInput['integerFields'][] = [
            "name" => "new integer field name",
            "description" => "new integer field description",
            "position" => "new integer field position",
            "mandatory" => true,
            "defaultValue" => 100,
            "minValue" => 64,
            "maxValue" => 512,
            "placeholder" => "new integer field placeholder",
        ];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    public function test_add()
    {
        $response = [
            "name" => $this->feedbackFormInput['integerFields'][0]['name'],
            "description" => $this->feedbackFormInput['integerFields'][0]['description'],
            "position" => $this->feedbackFormInput['integerFields'][0]['position'],
            "mandatory" => $this->feedbackFormInput['integerFields'][0]['mandatory'],
            "defaultValue" => $this->feedbackFormInput['integerFields'][0]['defaultValue'],
            "minValue" => $this->feedbackFormInput['integerFields'][0]['minValue'],
            "maxValue" => $this->feedbackFormInput['integerFields'][0]['maxValue'],
            "placeholder" => $this->feedbackFormInput['integerFields'][0]['placeholder'],
        ];
        
        $this->post($this->feedbackFormUri, $this->feedbackFormInput, $this->manager->token)
            ->seeStatusCode(201)
            ->seeJsonContains($response);
        
        $integerFieldEntry = [
            "name" => $this->feedbackFormInput['integerFields'][0]['name'],
            "description" => $this->feedbackFormInput['integerFields'][0]['description'],
            "position" => $this->feedbackFormInput['integerFields'][0]['position'],
            "mandatory" => $this->feedbackFormInput['integerFields'][0]['mandatory'],
            "defaultValue" => $this->feedbackFormInput['integerFields'][0]['defaultValue'],
            "minimumValue" => $this->feedbackFormInput['integerFields'][0]['minValue'],
            "maximumValue" => $this->feedbackFormInput['integerFields'][0]['maxValue'],
            "placeholder" => $this->feedbackFormInput['integerFields'][0]['placeholder'],
            "removed" => false,
        ];
        $this->seeInDatabase('IntegerField', $integerFieldEntry);
    }
    public function test_update_fieldInputWithoutId_addAsNewField()
    {
        $response = [
            "name" => $this->feedbackFormInput['integerFields'][0]['name'],
            "description" => $this->feedbackFormInput['integerFields'][0]['description'],
            "position" => $this->feedbackFormInput['integerFields'][0]['position'],
            "mandatory" => $this->feedbackFormInput['integerFields'][0]['mandatory'],
            "defaultValue" => $this->feedbackFormInput['integerFields'][0]['defaultValue'],
            "minValue" => $this->feedbackFormInput['integerFields'][0]['minValue'],
            "maxValue" => $this->feedbackFormInput['integerFields'][0]['maxValue'],
            "placeholder" => $this->feedbackFormInput['integerFields'][0]['placeholder'],
        ];
        
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->patch($uri, $this->feedbackFormInput, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
        
        $integerFieldEntry = [
            "Form_id" => $this->feedbackForm->id,
            "name" => $this->feedbackFormInput['integerFields'][0]['name'],
            "description" => $this->feedbackFormInput['integerFields'][0]['description'],
            "position" => $this->feedbackFormInput['integerFields'][0]['position'],
            "mandatory" => $this->feedbackFormInput['integerFields'][0]['mandatory'],
            "defaultValue" => $this->feedbackFormInput['integerFields'][0]['defaultValue'],
            "minimumValue" => $this->feedbackFormInput['integerFields'][0]['minValue'],
            "maximumValue" => $this->feedbackFormInput['integerFields'][0]['maxValue'],
            "placeholder" => $this->feedbackFormInput['integerFields'][0]['placeholder'],
            "removed" => false,
        ];
        $this->seeInDatabase('IntegerField', $integerFieldEntry);
    }
    public function test_update_fieldInputWithId_updateExistingFieldWithSameId()
    {
        $this->feedbackFormInput['integerFields'][0]['id'] = $this->integerField->id;
        $response = [
            "id" => $this->feedbackForm->id,
            "name" => $this->feedbackFormInput['name'],
            "description" => $this->feedbackFormInput['description'],
            "integerFields" => [
                [
                    "id" => $this->integerField->id,
                    "name" => $this->feedbackFormInput['integerFields'][0]['name'],
                    "description" => $this->feedbackFormInput['integerFields'][0]['description'],
                    "position" => $this->feedbackFormInput['integerFields'][0]['position'],
                    "mandatory" => $this->feedbackFormInput['integerFields'][0]['mandatory'],
                    "defaultValue" => $this->feedbackFormInput['integerFields'][0]['defaultValue'],
                    "minValue" => $this->feedbackFormInput['integerFields'][0]['minValue'],
                    "maxValue" => $this->feedbackFormInput['integerFields'][0]['maxValue'],
                    "placeholder" => $this->feedbackFormInput['integerFields'][0]['placeholder'],
                ],
            ],
        ];
        
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->patch($uri, $this->feedbackFormInput, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
        
        $integerFieldEntry = [
            "Form_id" => $this->feedbackForm->id,
            "id" => $this->integerField->id,
            "name" => $this->feedbackFormInput['integerFields'][0]['name'],
            "description" => $this->feedbackFormInput['integerFields'][0]['description'],
            "position" => $this->feedbackFormInput['integerFields'][0]['position'],
            "mandatory" => $this->feedbackFormInput['integerFields'][0]['mandatory'],
            "defaultValue" => $this->feedbackFormInput['integerFields'][0]['defaultValue'],
            "minimumValue" => $this->feedbackFormInput['integerFields'][0]['minValue'],
            "maximumValue" => $this->feedbackFormInput['integerFields'][0]['maxValue'],
            "placeholder" => $this->feedbackFormInput['integerFields'][0]['placeholder'],
            "removed" => false,
        ];
        $this->seeInDatabase('IntegerField', $integerFieldEntry);
    }
    public function test_update_existingFieldNotMentionInUpdateInput_removeThisField()
    {
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->patch($uri, $this->feedbackFormInput, $this->manager->token)
            ->seeStatusCode(200);
        
        $integerFieldEntry = [
            "Form_id" => $this->feedbackForm->id,
            "id" => $this->integerField->id,
            "removed" => true,
        ];
        $this->seeInDatabase('IntegerField', $integerFieldEntry);
    }
    public function test_update_fieldInputWithIdNotFoundIndRecord_addAsNewField()
    {
        $this->feedbackFormInput['integerFields'][0]['id'] = 'not existing id';
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->patch($uri, $this->feedbackFormInput, $this->manager->token)
            ->seeStatusCode(200);
        
        $removedIntegerField = [
            "Form_id" => $this->feedbackForm->id,
            "id" => $this->integerField->id,
            "removed" => true,
        ];
        $this->seeInDatabase('IntegerField', $removedIntegerField);
        
        $newIntegerFieldRecord = [
            "Form_id" => $this->feedbackForm->id,
            "name" => $this->feedbackFormInput['integerFields'][0]['name'],
            "description" => $this->feedbackFormInput['integerFields'][0]['description'],
            "position" => $this->feedbackFormInput['integerFields'][0]['position'],
            "mandatory" => $this->feedbackFormInput['integerFields'][0]['mandatory'],
            "defaultValue" => $this->feedbackFormInput['integerFields'][0]['defaultValue'],
            "minimumValue" => $this->feedbackFormInput['integerFields'][0]['minValue'],
            "maximumValue" => $this->feedbackFormInput['integerFields'][0]['maxValue'],
            "placeholder" => $this->feedbackFormInput['integerFields'][0]['placeholder'],
            "removed" => false,
        ];
        $this->seeInDatabase('IntegerField', $newIntegerFieldRecord);
    }
    public function test_show()
    {
        $response = [
            "id" => $this->feedbackForm->id,
            "name" => $this->feedbackForm->form->name,
            "description" => $this->feedbackForm->form->description,
            "integerFields" => [
                [
                    "id" => $this->integerField->id,
                    "name" => $this->integerField->name,
                    "description" => $this->integerField->description,
                    "position" => $this->integerField->position,
                    "mandatory" => $this->integerField->mandatory,
                    "defaultValue" => $this->integerField->defaultValue,
                    "minValue" => $this->integerField->minValue,
                    "maxValue" => $this->integerField->maxValue,
                    "placeholder" => $this->integerField->placeholder,
                ],
            ],
        ];
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->get($uri, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
}
