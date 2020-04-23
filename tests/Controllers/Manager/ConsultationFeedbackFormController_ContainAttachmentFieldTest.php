<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfAttachmentField;

class ConsultationFeedbackFormController_ContainAttachmentFieldTest extends ConsultationFeedbackFormTestCase
{
    protected $attachmentField;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->attachmentField = new RecordOfAttachmentField($this->consultationFeedbackForm->form, 0);
        $this->connection->table('AttachmentField')->insert($this->attachmentField->toArrayForDbEntry());
        
        $this->consultationFeedbackFormInput['attachmentFields'][] = [
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
            "name" => $this->consultationFeedbackFormInput['attachmentFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['attachmentFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['attachmentFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['attachmentFields'][0]['mandatory'],
            "minValue" => $this->consultationFeedbackFormInput['attachmentFields'][0]['minValue'],
            "maxValue" => $this->consultationFeedbackFormInput['attachmentFields'][0]['maxValue'],
        ];
        
        $this->post($this->consultationFeedbackFormUri, $this->consultationFeedbackFormInput, $this->manager->token)
            ->seeStatusCode(201)
            ->seeJsonContains($response);
        
        $attachmentFieldEntry = [
            "name" => $this->consultationFeedbackFormInput['attachmentFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['attachmentFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['attachmentFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['attachmentFields'][0]['mandatory'],
            "minimumValue" => $this->consultationFeedbackFormInput['attachmentFields'][0]['minValue'],
            "maximumValue" => $this->consultationFeedbackFormInput['attachmentFields'][0]['maxValue'],
            "removed" => false,
        ];
        $this->seeInDatabase('AttachmentField', $attachmentFieldEntry);
    }
    public function test_update_fieldInputWithoutId_addAsNewField()
    {
        $response = [
            "name" => $this->consultationFeedbackFormInput['attachmentFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['attachmentFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['attachmentFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['attachmentFields'][0]['mandatory'],
            "minValue" => $this->consultationFeedbackFormInput['attachmentFields'][0]['minValue'],
            "maxValue" => $this->consultationFeedbackFormInput['attachmentFields'][0]['maxValue'],
        ];
        
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->patch($uri, $this->consultationFeedbackFormInput, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
        
        $attachmentFieldEntry = [
            "Form_id" => $this->consultationFeedbackForm->form->id,
            "name" => $this->consultationFeedbackFormInput['attachmentFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['attachmentFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['attachmentFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['attachmentFields'][0]['mandatory'],
            "minimumValue" => $this->consultationFeedbackFormInput['attachmentFields'][0]['minValue'],
            "maximumValue" => $this->consultationFeedbackFormInput['attachmentFields'][0]['maxValue'],
            "removed" => false,
        ];
        $this->seeInDatabase('AttachmentField', $attachmentFieldEntry);
    }
    public function test_update_fieldInputWithId_updateExistingFieldWithSameId()
    {
        $this->consultationFeedbackFormInput['attachmentFields'][0]['id'] = $this->attachmentField->id;
        $response = [
            "id" => $this->consultationFeedbackForm->id,
            "name" => $this->consultationFeedbackFormInput['name'],
            "description" => $this->consultationFeedbackFormInput['description'],
            "attachmentFields" => [
                [
                    "id" => $this->attachmentField->id,
                    "name" => $this->consultationFeedbackFormInput['attachmentFields'][0]['name'],
                    "description" => $this->consultationFeedbackFormInput['attachmentFields'][0]['description'],
                    "position" => $this->consultationFeedbackFormInput['attachmentFields'][0]['position'],
                    "mandatory" => $this->consultationFeedbackFormInput['attachmentFields'][0]['mandatory'],
                    "minValue" => $this->consultationFeedbackFormInput['attachmentFields'][0]['minValue'],
                    "maxValue" => $this->consultationFeedbackFormInput['attachmentFields'][0]['maxValue'],
                ],
            ],
        ];
        
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->patch($uri, $this->consultationFeedbackFormInput, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
        
        $attachmentFieldEntry = [
            "Form_id" => $this->consultationFeedbackForm->form->id,
            "id" => $this->attachmentField->id,
            "name" => $this->consultationFeedbackFormInput['attachmentFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['attachmentFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['attachmentFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['attachmentFields'][0]['mandatory'],
            "minimumValue" => $this->consultationFeedbackFormInput['attachmentFields'][0]['minValue'],
            "maximumValue" => $this->consultationFeedbackFormInput['attachmentFields'][0]['maxValue'],
            "removed" => false,
        ];
        $this->seeInDatabase('AttachmentField', $attachmentFieldEntry);
    }
    public function test_update_existingFieldNotMentionInUpdateInput_removeThisField()
    {
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->patch($uri, $this->consultationFeedbackFormInput, $this->manager->token)
            ->seeStatusCode(200);
        
        $attachmentFieldEntry = [
            "Form_id" => $this->consultationFeedbackForm->form->id,
            "id" => $this->attachmentField->id,
            "removed" => true,
        ];
        $this->seeInDatabase('AttachmentField', $attachmentFieldEntry);
    }
    public function test_update_fieldInputWithIdNotFoundIndRecord_addAsNewField()
    {
        $this->consultationFeedbackFormInput['attachmentFields'][0]['id'] = 'not existing id';
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->patch($uri, $this->consultationFeedbackFormInput, $this->manager->token)
            ->seeStatusCode(200);
        
        $removedAttachmentField = [
            "Form_id" => $this->consultationFeedbackForm->id,
            "id" => $this->attachmentField->id,
            "removed" => true,
        ];
        $this->seeInDatabase('AttachmentField', $removedAttachmentField);
        
        $newAttachmentFieldEntry = [
            "Form_id" => $this->consultationFeedbackForm->form->id,
            "name" => $this->consultationFeedbackFormInput['attachmentFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['attachmentFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['attachmentFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['attachmentFields'][0]['mandatory'],
            "minimumValue" => $this->consultationFeedbackFormInput['attachmentFields'][0]['minValue'],
            "maximumValue" => $this->consultationFeedbackFormInput['attachmentFields'][0]['maxValue'],
            "removed" => false,
        ];
        $this->seeInDatabase('AttachmentField', $newAttachmentFieldEntry);
    }
    public function test_show()
    {
        $response = [
            "id" => $this->consultationFeedbackForm->id,
            "name" => $this->consultationFeedbackForm->form->name,
            "description" => $this->consultationFeedbackForm->form->description,
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
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->get($uri, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
}
