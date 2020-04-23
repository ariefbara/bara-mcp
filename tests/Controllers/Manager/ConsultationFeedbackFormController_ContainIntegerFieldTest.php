<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfIntegerField;

class ConsultationFeedbackFormController_ContainIntegerFieldTest extends ConsultationFeedbackFormTestCase
{
    protected $integerField;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->integerField = new RecordOfIntegerField($this->consultationFeedbackForm->form, 0);
        $this->connection->table('IntegerField')->insert($this->integerField->toArrayForDbEntry());
        
        $this->consultationFeedbackFormInput['integerFields'][] = [
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
            "name" => $this->consultationFeedbackFormInput['integerFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['integerFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['integerFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['integerFields'][0]['mandatory'],
            "defaultValue" => $this->consultationFeedbackFormInput['integerFields'][0]['defaultValue'],
            "minValue" => $this->consultationFeedbackFormInput['integerFields'][0]['minValue'],
            "maxValue" => $this->consultationFeedbackFormInput['integerFields'][0]['maxValue'],
            "placeholder" => $this->consultationFeedbackFormInput['integerFields'][0]['placeholder'],
        ];
        
        $this->post($this->consultationFeedbackFormUri, $this->consultationFeedbackFormInput, $this->manager->token)
            ->seeStatusCode(201)
            ->seeJsonContains($response);
        
        $integerFieldEntry = [
            "name" => $this->consultationFeedbackFormInput['integerFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['integerFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['integerFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['integerFields'][0]['mandatory'],
            "defaultValue" => $this->consultationFeedbackFormInput['integerFields'][0]['defaultValue'],
            "minimumValue" => $this->consultationFeedbackFormInput['integerFields'][0]['minValue'],
            "maximumValue" => $this->consultationFeedbackFormInput['integerFields'][0]['maxValue'],
            "placeholder" => $this->consultationFeedbackFormInput['integerFields'][0]['placeholder'],
            "removed" => false,
        ];
        $this->seeInDatabase('IntegerField', $integerFieldEntry);
    }
    public function test_update_fieldInputWithoutId_addAsNewField()
    {
        $response = [
            "name" => $this->consultationFeedbackFormInput['integerFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['integerFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['integerFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['integerFields'][0]['mandatory'],
            "defaultValue" => $this->consultationFeedbackFormInput['integerFields'][0]['defaultValue'],
            "minValue" => $this->consultationFeedbackFormInput['integerFields'][0]['minValue'],
            "maxValue" => $this->consultationFeedbackFormInput['integerFields'][0]['maxValue'],
            "placeholder" => $this->consultationFeedbackFormInput['integerFields'][0]['placeholder'],
        ];
        
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->patch($uri, $this->consultationFeedbackFormInput, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
        
        $integerFieldEntry = [
            "Form_id" => $this->consultationFeedbackForm->id,
            "name" => $this->consultationFeedbackFormInput['integerFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['integerFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['integerFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['integerFields'][0]['mandatory'],
            "defaultValue" => $this->consultationFeedbackFormInput['integerFields'][0]['defaultValue'],
            "minimumValue" => $this->consultationFeedbackFormInput['integerFields'][0]['minValue'],
            "maximumValue" => $this->consultationFeedbackFormInput['integerFields'][0]['maxValue'],
            "placeholder" => $this->consultationFeedbackFormInput['integerFields'][0]['placeholder'],
            "removed" => false,
        ];
        $this->seeInDatabase('IntegerField', $integerFieldEntry);
    }
    public function test_update_fieldInputWithId_updateExistingFieldWithSameId()
    {
        $this->consultationFeedbackFormInput['integerFields'][0]['id'] = $this->integerField->id;
        $response = [
            "id" => $this->consultationFeedbackForm->id,
            "name" => $this->consultationFeedbackFormInput['name'],
            "description" => $this->consultationFeedbackFormInput['description'],
            "integerFields" => [
                [
                    "id" => $this->integerField->id,
                    "name" => $this->consultationFeedbackFormInput['integerFields'][0]['name'],
                    "description" => $this->consultationFeedbackFormInput['integerFields'][0]['description'],
                    "position" => $this->consultationFeedbackFormInput['integerFields'][0]['position'],
                    "mandatory" => $this->consultationFeedbackFormInput['integerFields'][0]['mandatory'],
                    "defaultValue" => $this->consultationFeedbackFormInput['integerFields'][0]['defaultValue'],
                    "minValue" => $this->consultationFeedbackFormInput['integerFields'][0]['minValue'],
                    "maxValue" => $this->consultationFeedbackFormInput['integerFields'][0]['maxValue'],
                    "placeholder" => $this->consultationFeedbackFormInput['integerFields'][0]['placeholder'],
                ],
            ],
        ];
        
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->patch($uri, $this->consultationFeedbackFormInput, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
        
        $integerFieldEntry = [
            "Form_id" => $this->consultationFeedbackForm->id,
            "id" => $this->integerField->id,
            "name" => $this->consultationFeedbackFormInput['integerFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['integerFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['integerFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['integerFields'][0]['mandatory'],
            "defaultValue" => $this->consultationFeedbackFormInput['integerFields'][0]['defaultValue'],
            "minimumValue" => $this->consultationFeedbackFormInput['integerFields'][0]['minValue'],
            "maximumValue" => $this->consultationFeedbackFormInput['integerFields'][0]['maxValue'],
            "placeholder" => $this->consultationFeedbackFormInput['integerFields'][0]['placeholder'],
            "removed" => false,
        ];
        $this->seeInDatabase('IntegerField', $integerFieldEntry);
    }
    public function test_update_existingFieldNotMentionInUpdateInput_removeThisField()
    {
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->patch($uri, $this->consultationFeedbackFormInput, $this->manager->token)
            ->seeStatusCode(200);
        
        $integerFieldEntry = [
            "Form_id" => $this->consultationFeedbackForm->id,
            "id" => $this->integerField->id,
            "removed" => true,
        ];
        $this->seeInDatabase('IntegerField', $integerFieldEntry);
    }
    public function test_update_fieldInputWithIdNotFoundIndRecord_addAsNewField()
    {
        $this->consultationFeedbackFormInput['integerFields'][0]['id'] = 'not existing id';
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->patch($uri, $this->consultationFeedbackFormInput, $this->manager->token)
            ->seeStatusCode(200);
        
        $removedIntegerField = [
            "Form_id" => $this->consultationFeedbackForm->id,
            "id" => $this->integerField->id,
            "removed" => true,
        ];
        $this->seeInDatabase('IntegerField', $removedIntegerField);
        
        $newIntegerFieldRecord = [
            "Form_id" => $this->consultationFeedbackForm->id,
            "name" => $this->consultationFeedbackFormInput['integerFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['integerFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['integerFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['integerFields'][0]['mandatory'],
            "defaultValue" => $this->consultationFeedbackFormInput['integerFields'][0]['defaultValue'],
            "minimumValue" => $this->consultationFeedbackFormInput['integerFields'][0]['minValue'],
            "maximumValue" => $this->consultationFeedbackFormInput['integerFields'][0]['maxValue'],
            "placeholder" => $this->consultationFeedbackFormInput['integerFields'][0]['placeholder'],
            "removed" => false,
        ];
        $this->seeInDatabase('IntegerField', $newIntegerFieldRecord);
    }
    public function test_show()
    {
        $response = [
            "id" => $this->consultationFeedbackForm->id,
            "name" => $this->consultationFeedbackForm->form->name,
            "description" => $this->consultationFeedbackForm->form->description,
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
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->get($uri, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
}
