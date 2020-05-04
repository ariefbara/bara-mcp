<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfStringField;

class FeedbackFormController_ContainStringFieldTest extends FeedbackFormTestCase
{
    protected $stringField;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->stringField = new RecordOfStringField($this->feedbackForm->form, 0);
        $this->connection->table('StringField')->insert($this->stringField->toArrayForDbEntry());
        
        $this->feedbackFormInput['stringFields'][] = [
            "name" => "new string field name",
            "description" => "new string field description",
            "position" => "new string field position",
            "mandatory" => true,
            "defaultValue" => "new string field defaultValue",
            "minValue" => 64,
            "maxValue" => 512,
            "placeholder" => "new string field placeholder",
        ];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    public function test_add()
    {
$this->disableExceptionHandling();
        $response = [
            "name" => $this->feedbackFormInput['stringFields'][0]['name'],
            "description" => $this->feedbackFormInput['stringFields'][0]['description'],
            "position" => $this->feedbackFormInput['stringFields'][0]['position'],
            "mandatory" => $this->feedbackFormInput['stringFields'][0]['mandatory'],
            "defaultValue" => $this->feedbackFormInput['stringFields'][0]['defaultValue'],
            "minValue" => $this->feedbackFormInput['stringFields'][0]['minValue'],
            "maxValue" => $this->feedbackFormInput['stringFields'][0]['maxValue'],
            "placeholder" => $this->feedbackFormInput['stringFields'][0]['placeholder'],
        ];
        
        $this->post($this->feedbackFormUri, $this->feedbackFormInput, $this->manager->token)
            ->seeStatusCode(201)
            ->seeJsonContains($response);
        
        $stringFieldRecord = [
            "name" => $this->feedbackFormInput['stringFields'][0]['name'],
            "description" => $this->feedbackFormInput['stringFields'][0]['description'],
            "position" => $this->feedbackFormInput['stringFields'][0]['position'],
            "mandatory" => $this->feedbackFormInput['stringFields'][0]['mandatory'],
            "defaultValue" => $this->feedbackFormInput['stringFields'][0]['defaultValue'],
            "minimumValue" => $this->feedbackFormInput['stringFields'][0]['minValue'],
            "maximumValue" => $this->feedbackFormInput['stringFields'][0]['maxValue'],
            "placeholder" => $this->feedbackFormInput['stringFields'][0]['placeholder'],
            "removed" => false,
        ];
        $this->seeInDatabase('StringField', $stringFieldRecord);
    }
    public function test_update_fieldInputWithoutId_addAsNewField()
    {
        $response = [
            "name" => $this->feedbackFormInput['stringFields'][0]['name'],
            "description" => $this->feedbackFormInput['stringFields'][0]['description'],
            "position" => $this->feedbackFormInput['stringFields'][0]['position'],
            "mandatory" => $this->feedbackFormInput['stringFields'][0]['mandatory'],
            "defaultValue" => $this->feedbackFormInput['stringFields'][0]['defaultValue'],
            "minValue" => $this->feedbackFormInput['stringFields'][0]['minValue'],
            "maxValue" => $this->feedbackFormInput['stringFields'][0]['maxValue'],
            "placeholder" => $this->feedbackFormInput['stringFields'][0]['placeholder'],
        ];
        
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->patch($uri, $this->feedbackFormInput, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
        
        $stringFieldRecord = [
            "Form_id" => $this->feedbackForm->id,
            "name" => $this->feedbackFormInput['stringFields'][0]['name'],
            "description" => $this->feedbackFormInput['stringFields'][0]['description'],
            "position" => $this->feedbackFormInput['stringFields'][0]['position'],
            "mandatory" => $this->feedbackFormInput['stringFields'][0]['mandatory'],
            "defaultValue" => $this->feedbackFormInput['stringFields'][0]['defaultValue'],
            "minimumValue" => $this->feedbackFormInput['stringFields'][0]['minValue'],
            "maximumValue" => $this->feedbackFormInput['stringFields'][0]['maxValue'],
            "placeholder" => $this->feedbackFormInput['stringFields'][0]['placeholder'],
            "removed" => false,
        ];
        $this->seeInDatabase('StringField', $stringFieldRecord);
    }
    public function test_update_fieldInputWithId_updateExistingFieldWithSameId()
    {
        $this->feedbackFormInput['stringFields'][0]['id'] = $this->stringField->id;
        $response = [
            "id" => $this->feedbackForm->id,
            "name" => $this->feedbackFormInput['name'],
            "description" => $this->feedbackFormInput['description'],
            "stringFields" => [
                [
                    "id" => $this->stringField->id,
                    "name" => $this->feedbackFormInput['stringFields'][0]['name'],
                    "description" => $this->feedbackFormInput['stringFields'][0]['description'],
                    "position" => $this->feedbackFormInput['stringFields'][0]['position'],
                    "mandatory" => $this->feedbackFormInput['stringFields'][0]['mandatory'],
                    "defaultValue" => $this->feedbackFormInput['stringFields'][0]['defaultValue'],
                    "minValue" => $this->feedbackFormInput['stringFields'][0]['minValue'],
                    "maxValue" => $this->feedbackFormInput['stringFields'][0]['maxValue'],
                    "placeholder" => $this->feedbackFormInput['stringFields'][0]['placeholder'],
                ],
            ],
        ];
        
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->patch($uri, $this->feedbackFormInput, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
        
        $stringFieldRecord = [
            "Form_id" => $this->feedbackForm->id,
            "id" => $this->stringField->id,
            "name" => $this->feedbackFormInput['stringFields'][0]['name'],
            "description" => $this->feedbackFormInput['stringFields'][0]['description'],
            "position" => $this->feedbackFormInput['stringFields'][0]['position'],
            "mandatory" => $this->feedbackFormInput['stringFields'][0]['mandatory'],
            "defaultValue" => $this->feedbackFormInput['stringFields'][0]['defaultValue'],
            "minimumValue" => $this->feedbackFormInput['stringFields'][0]['minValue'],
            "maximumValue" => $this->feedbackFormInput['stringFields'][0]['maxValue'],
            "placeholder" => $this->feedbackFormInput['stringFields'][0]['placeholder'],
            "removed" => false,
        ];
        $this->seeInDatabase('StringField', $stringFieldRecord);
    }
    public function test_update_existingFieldNotMentionInUpdateInput_removeThisField()
    {
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->patch($uri, $this->feedbackFormInput, $this->manager->token)
            ->seeStatusCode(200);
        
        $stringFieldRecord = [
            "Form_id" => $this->feedbackForm->id,
            "id" => $this->stringField->id,
            "removed" => true,
        ];
        $this->seeInDatabase('StringField', $stringFieldRecord);
    }
    public function test_update_fieldInputWithIdNotFoundIndRecord_addAsNewField()
    {
        $this->feedbackFormInput['stringFields'][0]['id'] = 'not existing id';
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->patch($uri, $this->feedbackFormInput, $this->manager->token)
            ->seeStatusCode(200);
        
        $removedStringField = [
            "Form_id" => $this->feedbackForm->id,
            "id" => $this->stringField->id,
            "removed" => true,
        ];
        $this->seeInDatabase('StringField', $removedStringField);
        
        $newStringFieldRecord = [
            "Form_id" => $this->feedbackForm->id,
            "name" => $this->feedbackFormInput['stringFields'][0]['name'],
            "description" => $this->feedbackFormInput['stringFields'][0]['description'],
            "position" => $this->feedbackFormInput['stringFields'][0]['position'],
            "mandatory" => $this->feedbackFormInput['stringFields'][0]['mandatory'],
            "defaultValue" => $this->feedbackFormInput['stringFields'][0]['defaultValue'],
            "minimumValue" => $this->feedbackFormInput['stringFields'][0]['minValue'],
            "maximumValue" => $this->feedbackFormInput['stringFields'][0]['maxValue'],
            "placeholder" => $this->feedbackFormInput['stringFields'][0]['placeholder'],
            "removed" => false,
        ];
        $this->seeInDatabase('StringField', $newStringFieldRecord);
    }
    public function test_show()
    {
        $response = [
            "id" => $this->feedbackForm->id,
            "name" => $this->feedbackForm->form->name,
            "description" => $this->feedbackForm->form->description,
            "stringFields" => [
                [
                    "id" => $this->stringField->id,
                    "name" => $this->stringField->name,
                    "description" => $this->stringField->description,
                    "position" => $this->stringField->position,
                    "mandatory" => $this->stringField->mandatory,
                    "defaultValue" => $this->stringField->defaultValue,
                    "minValue" => $this->stringField->minValue,
                    "maxValue" => $this->stringField->maxValue,
                    "placeholder" => $this->stringField->placeholder,
                ],
            ],
        ];
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->get($uri, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
}
