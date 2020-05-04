<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfAttachmentField;

class FeedbackFormController_ContainAttachmentFieldTest extends FeedbackFormTestCase
{
    protected $attachmentField;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->attachmentField = new RecordOfAttachmentField($this->feedbackForm->form, 0);
        $this->connection->table('AttachmentField')->insert($this->attachmentField->toArrayForDbEntry());
        
        $this->feedbackFormInput['attachmentFields'][] = [
            "name" => "new attachment field name",
            "description" => "new attachment field description",
            "position" => "new attachment field position",
            "mandatory" => true,
            "minValue" => 1,
            "maxValue" => 4,
        ];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    public function test_add()
    {
        $response = [
            "name" => $this->feedbackFormInput['attachmentFields'][0]['name'],
            "description" => $this->feedbackFormInput['attachmentFields'][0]['description'],
            "position" => $this->feedbackFormInput['attachmentFields'][0]['position'],
            "mandatory" => $this->feedbackFormInput['attachmentFields'][0]['mandatory'],
            "minValue" => $this->feedbackFormInput['attachmentFields'][0]['minValue'],
            "maxValue" => $this->feedbackFormInput['attachmentFields'][0]['maxValue'],
        ];
        
        $this->post($this->feedbackFormUri, $this->feedbackFormInput, $this->manager->token)
            ->seeStatusCode(201)
            ->seeJsonContains($response);
        
        $attachmentFieldEntry = [
            "name" => $this->feedbackFormInput['attachmentFields'][0]['name'],
            "description" => $this->feedbackFormInput['attachmentFields'][0]['description'],
            "position" => $this->feedbackFormInput['attachmentFields'][0]['position'],
            "mandatory" => $this->feedbackFormInput['attachmentFields'][0]['mandatory'],
            "minimumValue" => $this->feedbackFormInput['attachmentFields'][0]['minValue'],
            "maximumValue" => $this->feedbackFormInput['attachmentFields'][0]['maxValue'],
            "removed" => false,
        ];
        $this->seeInDatabase('AttachmentField', $attachmentFieldEntry);
    }
    public function test_update_fieldInputWithoutId_addAsNewField()
    {
        $response = [
            "name" => $this->feedbackFormInput['attachmentFields'][0]['name'],
            "description" => $this->feedbackFormInput['attachmentFields'][0]['description'],
            "position" => $this->feedbackFormInput['attachmentFields'][0]['position'],
            "mandatory" => $this->feedbackFormInput['attachmentFields'][0]['mandatory'],
            "minValue" => $this->feedbackFormInput['attachmentFields'][0]['minValue'],
            "maxValue" => $this->feedbackFormInput['attachmentFields'][0]['maxValue'],
        ];
        
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->patch($uri, $this->feedbackFormInput, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
        
        $attachmentFieldEntry = [
            "Form_id" => $this->feedbackForm->form->id,
            "name" => $this->feedbackFormInput['attachmentFields'][0]['name'],
            "description" => $this->feedbackFormInput['attachmentFields'][0]['description'],
            "position" => $this->feedbackFormInput['attachmentFields'][0]['position'],
            "mandatory" => $this->feedbackFormInput['attachmentFields'][0]['mandatory'],
            "minimumValue" => $this->feedbackFormInput['attachmentFields'][0]['minValue'],
            "maximumValue" => $this->feedbackFormInput['attachmentFields'][0]['maxValue'],
            "removed" => false,
        ];
        $this->seeInDatabase('AttachmentField', $attachmentFieldEntry);
    }
    public function test_update_fieldInputWithId_updateExistingFieldWithSameId()
    {
        $this->feedbackFormInput['attachmentFields'][0]['id'] = $this->attachmentField->id;
        $response = [
            "id" => $this->feedbackForm->id,
            "name" => $this->feedbackFormInput['name'],
            "description" => $this->feedbackFormInput['description'],
            "attachmentFields" => [
                [
                    "id" => $this->attachmentField->id,
                    "name" => $this->feedbackFormInput['attachmentFields'][0]['name'],
                    "description" => $this->feedbackFormInput['attachmentFields'][0]['description'],
                    "position" => $this->feedbackFormInput['attachmentFields'][0]['position'],
                    "mandatory" => $this->feedbackFormInput['attachmentFields'][0]['mandatory'],
                    "minValue" => $this->feedbackFormInput['attachmentFields'][0]['minValue'],
                    "maxValue" => $this->feedbackFormInput['attachmentFields'][0]['maxValue'],
                ],
            ],
        ];
        
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->patch($uri, $this->feedbackFormInput, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
        
        $attachmentFieldEntry = [
            "Form_id" => $this->feedbackForm->form->id,
            "id" => $this->attachmentField->id,
            "name" => $this->feedbackFormInput['attachmentFields'][0]['name'],
            "description" => $this->feedbackFormInput['attachmentFields'][0]['description'],
            "position" => $this->feedbackFormInput['attachmentFields'][0]['position'],
            "mandatory" => $this->feedbackFormInput['attachmentFields'][0]['mandatory'],
            "minimumValue" => $this->feedbackFormInput['attachmentFields'][0]['minValue'],
            "maximumValue" => $this->feedbackFormInput['attachmentFields'][0]['maxValue'],
            "removed" => false,
        ];
        $this->seeInDatabase('AttachmentField', $attachmentFieldEntry);
    }
    public function test_update_existingFieldNotMentionInUpdateInput_removeThisField()
    {
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->patch($uri, $this->feedbackFormInput, $this->manager->token)
            ->seeStatusCode(200);
        
        $attachmentFieldEntry = [
            "Form_id" => $this->feedbackForm->form->id,
            "id" => $this->attachmentField->id,
            "removed" => true,
        ];
        $this->seeInDatabase('AttachmentField', $attachmentFieldEntry);
    }
    public function test_update_fieldInputWithIdNotFoundIndRecord_addAsNewField()
    {
        $this->feedbackFormInput['attachmentFields'][0]['id'] = 'not existing id';
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->patch($uri, $this->feedbackFormInput, $this->manager->token)
            ->seeStatusCode(200);
        
        $removedAttachmentField = [
            "Form_id" => $this->feedbackForm->id,
            "id" => $this->attachmentField->id,
            "removed" => true,
        ];
        $this->seeInDatabase('AttachmentField', $removedAttachmentField);
        
        $newAttachmentFieldEntry = [
            "Form_id" => $this->feedbackForm->form->id,
            "name" => $this->feedbackFormInput['attachmentFields'][0]['name'],
            "description" => $this->feedbackFormInput['attachmentFields'][0]['description'],
            "position" => $this->feedbackFormInput['attachmentFields'][0]['position'],
            "mandatory" => $this->feedbackFormInput['attachmentFields'][0]['mandatory'],
            "minimumValue" => $this->feedbackFormInput['attachmentFields'][0]['minValue'],
            "maximumValue" => $this->feedbackFormInput['attachmentFields'][0]['maxValue'],
            "removed" => false,
        ];
        $this->seeInDatabase('AttachmentField', $newAttachmentFieldEntry);
    }
    public function test_show()
    {
        $response = [
            "id" => $this->feedbackForm->id,
            "name" => $this->feedbackForm->form->name,
            "description" => $this->feedbackForm->form->description,
            "attachmentFields" => [
                [
                    "id" => $this->attachmentField->id,
                    "name" => $this->attachmentField->name,
                    "description" => $this->attachmentField->description,
                    "position" => $this->attachmentField->position,
                    "mandatory" => $this->attachmentField->mandatory,
                    "minValue" => $this->attachmentField->minValue,
                    "maxValue" => $this->attachmentField->maxValue,
                ],
            ],
        ];
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->get($uri, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
}
