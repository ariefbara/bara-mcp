<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfTextAreaField;

class ConsultationFeedbackFormController_ContainTextAreaField extends ConsultationFeedbackFormTestCase
{
    protected $textAreaField;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->textAreaField = new RecordOfTextAreaField($this->consultationFeedbackForm->form, 0);
        $this->connection->table('TextAreaField')->insert($this->textAreaField->toArrayForDbEntry());
        
        $this->consultationFeedbackFormInput['textAreaFields'][] = [
            "name" => "new text area field name",
            "description" => "new text area field description",
            "position" => "new text area field position",
            "mandatory" => true,
            "defaultValue" => 'text area field default value',
            "minValue" => 64,
            "maxValue" => 512,
            "placeholder" => "new text area field placeholder",
        ];
    }
    
    public function test_add()
    {
        $response = [
            "name" => $this->consultationFeedbackFormInput['textAreaFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['textAreaFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['textAreaFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['textAreaFields'][0]['mandatory'],
            "defaultValue" => $this->consultationFeedbackFormInput['textAreaFields'][0]['defaultValue'],
            "minValue" => $this->consultationFeedbackFormInput['textAreaFields'][0]['minValue'],
            "maxValue" => $this->consultationFeedbackFormInput['textAreaFields'][0]['maxValue'],
            "placeholder" => $this->consultationFeedbackFormInput['textAreaFields'][0]['placeholder'],
        ];
        
        $this->post($this->consultationFeedbackFormUri, $this->consultationFeedbackFormInput, $this->manager->token)
            ->seeStatusCode(201)
            ->seeJsonContains($response);
        
        $textAreaEntry = [
            "name" => $this->consultationFeedbackFormInput['textAreaFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['textAreaFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['textAreaFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['textAreaFields'][0]['mandatory'],
            "defaultValue" => $this->consultationFeedbackFormInput['textAreaFields'][0]['defaultValue'],
            "minimumValue" => $this->consultationFeedbackFormInput['textAreaFields'][0]['minValue'],
            "maximumValue" => $this->consultationFeedbackFormInput['textAreaFields'][0]['maxValue'],
            "placeholder" => $this->consultationFeedbackFormInput['textAreaFields'][0]['placeholder'],
            "removed" => false,
        ];
        $this->seeInDatabase('TextAreaField', $textAreaEntry);
    }
    public function test_update_fieldInputWithoutId_addAsNewField()
    {
        $response = [
            "name" => $this->consultationFeedbackFormInput['textAreaFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['textAreaFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['textAreaFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['textAreaFields'][0]['mandatory'],
            "defaultValue" => $this->consultationFeedbackFormInput['textAreaFields'][0]['defaultValue'],
            "minValue" => $this->consultationFeedbackFormInput['textAreaFields'][0]['minValue'],
            "maxValue" => $this->consultationFeedbackFormInput['textAreaFields'][0]['maxValue'],
            "placeholder" => $this->consultationFeedbackFormInput['textAreaFields'][0]['placeholder'],
        ];
        
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->patch($uri, $this->consultationFeedbackFormInput, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
        
        $textAreaEntry = [
            "Form_id" => $this->consultationFeedbackForm->id,
            "name" => $this->consultationFeedbackFormInput['textAreaFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['textAreaFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['textAreaFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['textAreaFields'][0]['mandatory'],
            "defaultValue" => $this->consultationFeedbackFormInput['textAreaFields'][0]['defaultValue'],
            "minimumValue" => $this->consultationFeedbackFormInput['textAreaFields'][0]['minValue'],
            "maximumValue" => $this->consultationFeedbackFormInput['textAreaFields'][0]['maxValue'],
            "placeholder" => $this->consultationFeedbackFormInput['textAreaFields'][0]['placeholder'],
            "removed" => false,
        ];
        $this->seeInDatabase('TextAreaField', $textAreaEntry);
    }
    public function test_update_fieldInputWithId_updateExistingFieldWithSameId()
    {
        $this->consultationFeedbackFormInput['textAreaFields'][0]['id'] = $this->textAreaField->id;
        $response = [
            "id" => $this->consultationFeedbackForm->id,
            "name" => $this->consultationFeedbackFormInput['name'],
            "description" => $this->consultationFeedbackFormInput['description'],
            "textAreaFields" => [
                [
                    "id" => $this->textAreaField->id,
                    "name" => $this->consultationFeedbackFormInput['textAreaFields'][0]['name'],
                    "description" => $this->consultationFeedbackFormInput['textAreaFields'][0]['description'],
                    "position" => $this->consultationFeedbackFormInput['textAreaFields'][0]['position'],
                    "mandatory" => $this->consultationFeedbackFormInput['textAreaFields'][0]['mandatory'],
                    "defaultValue" => $this->consultationFeedbackFormInput['textAreaFields'][0]['defaultValue'],
                    "minValue" => $this->consultationFeedbackFormInput['textAreaFields'][0]['minValue'],
                    "maxValue" => $this->consultationFeedbackFormInput['textAreaFields'][0]['maxValue'],
                    "placeholder" => $this->consultationFeedbackFormInput['textAreaFields'][0]['placeholder'],
                ],
            ],
        ];
        
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->patch($uri, $this->consultationFeedbackFormInput, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
        
        $textAreaEntry = [
            "Form_id" => $this->consultationFeedbackForm->id,
            "id" => $this->textAreaField->id,
            "name" => $this->consultationFeedbackFormInput['textAreaFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['textAreaFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['textAreaFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['textAreaFields'][0]['mandatory'],
            "defaultValue" => $this->consultationFeedbackFormInput['textAreaFields'][0]['defaultValue'],
            "minimumValue" => $this->consultationFeedbackFormInput['textAreaFields'][0]['minValue'],
            "maximumValue" => $this->consultationFeedbackFormInput['textAreaFields'][0]['maxValue'],
            "placeholder" => $this->consultationFeedbackFormInput['textAreaFields'][0]['placeholder'],
            "removed" => false,
        ];
        $this->seeInDatabase('TextAreaField', $textAreaEntry);
    }
    public function test_update_existingFieldNotMentionInUpdateInput_removeThisField()
    {
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->patch($uri, $this->consultationFeedbackFormInput, $this->manager->token)
            ->seeStatusCode(200);
        
        $textAreaEntry = [
            "Form_id" => $this->consultationFeedbackForm->id,
            "id" => $this->textAreaField->id,
            "removed" => true,
        ];
        $this->seeInDatabase('TextAreaField', $textAreaEntry);
    }
    public function test_update_fieldInputWithIdNotFoundIndRecord_addAsNewField()
    {
        $this->consultationFeedbackFormInput['textAreaFields'][0]['id'] = 'not existing id';
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->patch($uri, $this->consultationFeedbackFormInput, $this->manager->token)
            ->seeStatusCode(200);
        
        $removedTextAreaField = [
            "Form_id" => $this->consultationFeedbackForm->id,
            "id" => $this->textAreaField->id,
            "removed" => true,
        ];
        $this->seeInDatabase('TextAreaField', $removedTextAreaField);
        
        $newTextAreaFieldRecord = [
            "Form_id" => $this->consultationFeedbackForm->id,
            "name" => $this->consultationFeedbackFormInput['textAreaFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['textAreaFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['textAreaFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['textAreaFields'][0]['mandatory'],
            "defaultValue" => $this->consultationFeedbackFormInput['textAreaFields'][0]['defaultValue'],
            "minimumValue" => $this->consultationFeedbackFormInput['textAreaFields'][0]['minValue'],
            "maximumValue" => $this->consultationFeedbackFormInput['textAreaFields'][0]['maxValue'],
            "placeholder" => $this->consultationFeedbackFormInput['textAreaFields'][0]['placeholder'],
            "removed" => false,
        ];
        $this->seeInDatabase('TextAreaField', $newTextAreaFieldRecord);
    }
    public function test_show()
    {
        $response = [
            "id" => $this->consultationFeedbackForm->id,
            "name" => $this->consultationFeedbackForm->form->name,
            "description" => $this->consultationFeedbackForm->form->description,
            "textAreaFields" => [
                [
                    "id" => $this->textAreaField->id,
                    "name" => $this->textAreaField->name,
                    "description" => $this->textAreaField->description,
                    "position" => $this->textAreaField->position,
                    "mandatory" => $this->textAreaField->mandatory,
                    "defaultValue" => $this->textAreaField->defaultValue,
                    "minValue" => $this->textAreaField->minValue,
                    "maxValue" => $this->textAreaField->maxValue,
                    "placeholder" => $this->textAreaField->placeholder,
                ],
            ],
        ];
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->get($uri, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
}
