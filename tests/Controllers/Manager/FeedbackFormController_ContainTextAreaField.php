<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfTextAreaField;

class FeedbackFormController_ContainTextAreaField extends FeedbackFormTestCase
{
    protected $textAreaField;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->textAreaField = new RecordOfTextAreaField($this->feedbackForm->form, 0);
        $this->connection->table('TextAreaField')->insert($this->textAreaField->toArrayForDbEntry());
        
        $this->feedbackFormInput['textAreaFields'][] = [
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
            "name" => $this->feedbackFormInput['textAreaFields'][0]['name'],
            "description" => $this->feedbackFormInput['textAreaFields'][0]['description'],
            "position" => $this->feedbackFormInput['textAreaFields'][0]['position'],
            "mandatory" => $this->feedbackFormInput['textAreaFields'][0]['mandatory'],
            "defaultValue" => $this->feedbackFormInput['textAreaFields'][0]['defaultValue'],
            "minValue" => $this->feedbackFormInput['textAreaFields'][0]['minValue'],
            "maxValue" => $this->feedbackFormInput['textAreaFields'][0]['maxValue'],
            "placeholder" => $this->feedbackFormInput['textAreaFields'][0]['placeholder'],
        ];
        
        $this->post($this->feedbackFormUri, $this->feedbackFormInput, $this->manager->token)
            ->seeStatusCode(201)
            ->seeJsonContains($response);
        
        $textAreaEntry = [
            "name" => $this->feedbackFormInput['textAreaFields'][0]['name'],
            "description" => $this->feedbackFormInput['textAreaFields'][0]['description'],
            "position" => $this->feedbackFormInput['textAreaFields'][0]['position'],
            "mandatory" => $this->feedbackFormInput['textAreaFields'][0]['mandatory'],
            "defaultValue" => $this->feedbackFormInput['textAreaFields'][0]['defaultValue'],
            "minimumValue" => $this->feedbackFormInput['textAreaFields'][0]['minValue'],
            "maximumValue" => $this->feedbackFormInput['textAreaFields'][0]['maxValue'],
            "placeholder" => $this->feedbackFormInput['textAreaFields'][0]['placeholder'],
            "removed" => false,
        ];
        $this->seeInDatabase('TextAreaField', $textAreaEntry);
    }
    public function test_update_fieldInputWithoutId_addAsNewField()
    {
        $response = [
            "name" => $this->feedbackFormInput['textAreaFields'][0]['name'],
            "description" => $this->feedbackFormInput['textAreaFields'][0]['description'],
            "position" => $this->feedbackFormInput['textAreaFields'][0]['position'],
            "mandatory" => $this->feedbackFormInput['textAreaFields'][0]['mandatory'],
            "defaultValue" => $this->feedbackFormInput['textAreaFields'][0]['defaultValue'],
            "minValue" => $this->feedbackFormInput['textAreaFields'][0]['minValue'],
            "maxValue" => $this->feedbackFormInput['textAreaFields'][0]['maxValue'],
            "placeholder" => $this->feedbackFormInput['textAreaFields'][0]['placeholder'],
        ];
        
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->patch($uri, $this->feedbackFormInput, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
        
        $textAreaEntry = [
            "Form_id" => $this->feedbackForm->id,
            "name" => $this->feedbackFormInput['textAreaFields'][0]['name'],
            "description" => $this->feedbackFormInput['textAreaFields'][0]['description'],
            "position" => $this->feedbackFormInput['textAreaFields'][0]['position'],
            "mandatory" => $this->feedbackFormInput['textAreaFields'][0]['mandatory'],
            "defaultValue" => $this->feedbackFormInput['textAreaFields'][0]['defaultValue'],
            "minimumValue" => $this->feedbackFormInput['textAreaFields'][0]['minValue'],
            "maximumValue" => $this->feedbackFormInput['textAreaFields'][0]['maxValue'],
            "placeholder" => $this->feedbackFormInput['textAreaFields'][0]['placeholder'],
            "removed" => false,
        ];
        $this->seeInDatabase('TextAreaField', $textAreaEntry);
    }
    public function test_update_fieldInputWithId_updateExistingFieldWithSameId()
    {
        $this->feedbackFormInput['textAreaFields'][0]['id'] = $this->textAreaField->id;
        $response = [
            "id" => $this->feedbackForm->id,
            "name" => $this->feedbackFormInput['name'],
            "description" => $this->feedbackFormInput['description'],
            "textAreaFields" => [
                [
                    "id" => $this->textAreaField->id,
                    "name" => $this->feedbackFormInput['textAreaFields'][0]['name'],
                    "description" => $this->feedbackFormInput['textAreaFields'][0]['description'],
                    "position" => $this->feedbackFormInput['textAreaFields'][0]['position'],
                    "mandatory" => $this->feedbackFormInput['textAreaFields'][0]['mandatory'],
                    "defaultValue" => $this->feedbackFormInput['textAreaFields'][0]['defaultValue'],
                    "minValue" => $this->feedbackFormInput['textAreaFields'][0]['minValue'],
                    "maxValue" => $this->feedbackFormInput['textAreaFields'][0]['maxValue'],
                    "placeholder" => $this->feedbackFormInput['textAreaFields'][0]['placeholder'],
                ],
            ],
        ];
        
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->patch($uri, $this->feedbackFormInput, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
        
        $textAreaEntry = [
            "Form_id" => $this->feedbackForm->id,
            "id" => $this->textAreaField->id,
            "name" => $this->feedbackFormInput['textAreaFields'][0]['name'],
            "description" => $this->feedbackFormInput['textAreaFields'][0]['description'],
            "position" => $this->feedbackFormInput['textAreaFields'][0]['position'],
            "mandatory" => $this->feedbackFormInput['textAreaFields'][0]['mandatory'],
            "defaultValue" => $this->feedbackFormInput['textAreaFields'][0]['defaultValue'],
            "minimumValue" => $this->feedbackFormInput['textAreaFields'][0]['minValue'],
            "maximumValue" => $this->feedbackFormInput['textAreaFields'][0]['maxValue'],
            "placeholder" => $this->feedbackFormInput['textAreaFields'][0]['placeholder'],
            "removed" => false,
        ];
        $this->seeInDatabase('TextAreaField', $textAreaEntry);
    }
    public function test_update_existingFieldNotMentionInUpdateInput_removeThisField()
    {
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->patch($uri, $this->feedbackFormInput, $this->manager->token)
            ->seeStatusCode(200);
        
        $textAreaEntry = [
            "Form_id" => $this->feedbackForm->id,
            "id" => $this->textAreaField->id,
            "removed" => true,
        ];
        $this->seeInDatabase('TextAreaField', $textAreaEntry);
    }
    public function test_update_fieldInputWithIdNotFoundIndRecord_addAsNewField()
    {
        $this->feedbackFormInput['textAreaFields'][0]['id'] = 'not existing id';
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->patch($uri, $this->feedbackFormInput, $this->manager->token)
            ->seeStatusCode(200);
        
        $removedTextAreaField = [
            "Form_id" => $this->feedbackForm->id,
            "id" => $this->textAreaField->id,
            "removed" => true,
        ];
        $this->seeInDatabase('TextAreaField', $removedTextAreaField);
        
        $newTextAreaFieldRecord = [
            "Form_id" => $this->feedbackForm->id,
            "name" => $this->feedbackFormInput['textAreaFields'][0]['name'],
            "description" => $this->feedbackFormInput['textAreaFields'][0]['description'],
            "position" => $this->feedbackFormInput['textAreaFields'][0]['position'],
            "mandatory" => $this->feedbackFormInput['textAreaFields'][0]['mandatory'],
            "defaultValue" => $this->feedbackFormInput['textAreaFields'][0]['defaultValue'],
            "minimumValue" => $this->feedbackFormInput['textAreaFields'][0]['minValue'],
            "maximumValue" => $this->feedbackFormInput['textAreaFields'][0]['maxValue'],
            "placeholder" => $this->feedbackFormInput['textAreaFields'][0]['placeholder'],
            "removed" => false,
        ];
        $this->seeInDatabase('TextAreaField', $newTextAreaFieldRecord);
    }
    public function test_show()
    {
        $response = [
            "id" => $this->feedbackForm->id,
            "name" => $this->feedbackForm->form->name,
            "description" => $this->feedbackForm->form->description,
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
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->get($uri, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
}
