<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfStringField;

class ConsultationFeedbackFormController_ContainStringFieldTest extends ConsultationFeedbackFormTestCase
{
    protected $stringField;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->stringField = new RecordOfStringField($this->consultationFeedbackForm->form, 0);
        $this->connection->table('StringField')->insert($this->stringField->toArrayForDbEntry());
        
        $this->consultationFeedbackFormInput['stringFields'][] = [
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
            "name" => $this->consultationFeedbackFormInput['stringFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['stringFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['stringFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['stringFields'][0]['mandatory'],
            "defaultValue" => $this->consultationFeedbackFormInput['stringFields'][0]['defaultValue'],
            "minValue" => $this->consultationFeedbackFormInput['stringFields'][0]['minValue'],
            "maxValue" => $this->consultationFeedbackFormInput['stringFields'][0]['maxValue'],
            "placeholder" => $this->consultationFeedbackFormInput['stringFields'][0]['placeholder'],
        ];
        
        $this->post($this->consultationFeedbackFormUri, $this->consultationFeedbackFormInput, $this->manager->token)
            ->seeStatusCode(201)
            ->seeJsonContains($response);
        
        $stringFieldRecord = [
            "name" => $this->consultationFeedbackFormInput['stringFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['stringFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['stringFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['stringFields'][0]['mandatory'],
            "defaultValue" => $this->consultationFeedbackFormInput['stringFields'][0]['defaultValue'],
            "minimumValue" => $this->consultationFeedbackFormInput['stringFields'][0]['minValue'],
            "maximumValue" => $this->consultationFeedbackFormInput['stringFields'][0]['maxValue'],
            "placeholder" => $this->consultationFeedbackFormInput['stringFields'][0]['placeholder'],
            "removed" => false,
        ];
        $this->seeInDatabase('StringField', $stringFieldRecord);
    }
    public function test_update_fieldInputWithoutId_addAsNewField()
    {
        $response = [
            "name" => $this->consultationFeedbackFormInput['stringFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['stringFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['stringFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['stringFields'][0]['mandatory'],
            "defaultValue" => $this->consultationFeedbackFormInput['stringFields'][0]['defaultValue'],
            "minValue" => $this->consultationFeedbackFormInput['stringFields'][0]['minValue'],
            "maxValue" => $this->consultationFeedbackFormInput['stringFields'][0]['maxValue'],
            "placeholder" => $this->consultationFeedbackFormInput['stringFields'][0]['placeholder'],
        ];
        
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->patch($uri, $this->consultationFeedbackFormInput, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
        
        $stringFieldRecord = [
            "Form_id" => $this->consultationFeedbackForm->id,
            "name" => $this->consultationFeedbackFormInput['stringFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['stringFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['stringFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['stringFields'][0]['mandatory'],
            "defaultValue" => $this->consultationFeedbackFormInput['stringFields'][0]['defaultValue'],
            "minimumValue" => $this->consultationFeedbackFormInput['stringFields'][0]['minValue'],
            "maximumValue" => $this->consultationFeedbackFormInput['stringFields'][0]['maxValue'],
            "placeholder" => $this->consultationFeedbackFormInput['stringFields'][0]['placeholder'],
            "removed" => false,
        ];
        $this->seeInDatabase('StringField', $stringFieldRecord);
    }
    public function test_update_fieldInputWithId_updateExistingFieldWithSameId()
    {
        $this->consultationFeedbackFormInput['stringFields'][0]['id'] = $this->stringField->id;
        $response = [
            "id" => $this->consultationFeedbackForm->id,
            "name" => $this->consultationFeedbackFormInput['name'],
            "description" => $this->consultationFeedbackFormInput['description'],
            "stringFields" => [
                [
                    "id" => $this->stringField->id,
                    "name" => $this->consultationFeedbackFormInput['stringFields'][0]['name'],
                    "description" => $this->consultationFeedbackFormInput['stringFields'][0]['description'],
                    "position" => $this->consultationFeedbackFormInput['stringFields'][0]['position'],
                    "mandatory" => $this->consultationFeedbackFormInput['stringFields'][0]['mandatory'],
                    "defaultValue" => $this->consultationFeedbackFormInput['stringFields'][0]['defaultValue'],
                    "minValue" => $this->consultationFeedbackFormInput['stringFields'][0]['minValue'],
                    "maxValue" => $this->consultationFeedbackFormInput['stringFields'][0]['maxValue'],
                    "placeholder" => $this->consultationFeedbackFormInput['stringFields'][0]['placeholder'],
                ],
            ],
        ];
        
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->patch($uri, $this->consultationFeedbackFormInput, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
        
        $stringFieldRecord = [
            "Form_id" => $this->consultationFeedbackForm->id,
            "id" => $this->stringField->id,
            "name" => $this->consultationFeedbackFormInput['stringFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['stringFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['stringFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['stringFields'][0]['mandatory'],
            "defaultValue" => $this->consultationFeedbackFormInput['stringFields'][0]['defaultValue'],
            "minimumValue" => $this->consultationFeedbackFormInput['stringFields'][0]['minValue'],
            "maximumValue" => $this->consultationFeedbackFormInput['stringFields'][0]['maxValue'],
            "placeholder" => $this->consultationFeedbackFormInput['stringFields'][0]['placeholder'],
            "removed" => false,
        ];
        $this->seeInDatabase('StringField', $stringFieldRecord);
    }
    public function test_update_existingFieldNotMentionInUpdateInput_removeThisField()
    {
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->patch($uri, $this->consultationFeedbackFormInput, $this->manager->token)
            ->seeStatusCode(200);
        
        $stringFieldRecord = [
            "Form_id" => $this->consultationFeedbackForm->id,
            "id" => $this->stringField->id,
            "removed" => true,
        ];
        $this->seeInDatabase('StringField', $stringFieldRecord);
    }
    public function test_update_fieldInputWithIdNotFoundIndRecord_addAsNewField()
    {
        $this->consultationFeedbackFormInput['stringFields'][0]['id'] = 'not existing id';
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->patch($uri, $this->consultationFeedbackFormInput, $this->manager->token)
            ->seeStatusCode(200);
        
        $removedStringField = [
            "Form_id" => $this->consultationFeedbackForm->id,
            "id" => $this->stringField->id,
            "removed" => true,
        ];
        $this->seeInDatabase('StringField', $removedStringField);
        
        $newStringFieldRecord = [
            "Form_id" => $this->consultationFeedbackForm->id,
            "name" => $this->consultationFeedbackFormInput['stringFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['stringFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['stringFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['stringFields'][0]['mandatory'],
            "defaultValue" => $this->consultationFeedbackFormInput['stringFields'][0]['defaultValue'],
            "minimumValue" => $this->consultationFeedbackFormInput['stringFields'][0]['minValue'],
            "maximumValue" => $this->consultationFeedbackFormInput['stringFields'][0]['maxValue'],
            "placeholder" => $this->consultationFeedbackFormInput['stringFields'][0]['placeholder'],
            "removed" => false,
        ];
        $this->seeInDatabase('StringField', $newStringFieldRecord);
    }
    public function test_show()
    {
        $response = [
            "id" => $this->consultationFeedbackForm->id,
            "name" => $this->consultationFeedbackForm->form->name,
            "description" => $this->consultationFeedbackForm->form->description,
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
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->get($uri, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
}
