<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\RecordPreparation\Shared\Form\ {
    RecordOfSelectField,
    RecordOfSingleSelectField
};

class ConsultationFeedbackFormController_ContainSIngleSelectFieldTest extends ConsultationFeedbackFormTestCase
{
    protected $singleSelectField;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $selectField = new RecordOfSelectField(0);
        $this->connection->table('SelectField')->insert($selectField->toArrayForDbEntry());
        
        $this->singleSelectField = new RecordOfSingleSelectField($this->consultationFeedbackForm->form, $selectField);
        $this->connection->table('SingleSelectField')->insert($this->singleSelectField->toArrayForDbEntry());
        
        $this->consultationFeedbackFormInput['singleSelectFields'][] = [
            "name" => "new single select field name",
            "description" => "new single select field description",
            "position" => "new single select field position",
            "mandatory" => true,
            "defaultValue" => "new single select field default value",
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
            "name" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['mandatory'],
            "defaultValue" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['defaultValue'],
            "options" => [],
        ];
        
        $this->post($this->consultationFeedbackFormUri, $this->consultationFeedbackFormInput, $this->manager->token)
            ->seeStatusCode(201)
            ->seeJsonContains($response);
        
        $selectFieldEntry = [
            "name" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['mandatory'],
        ];
        $this->seeInDatabase("SelectField", $selectFieldEntry);
        
        $singleSelectFieldEntry = [
            "defaultValue" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['defaultValue'],
            "removed" => false,
        ];
        $this->seeInDatabase('SingleSelectField', $singleSelectFieldEntry);
    }
    public function test_update_fieldInputWithoutId_addAsNewField()
    {
        $response = [
            "name" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['mandatory'],
            "defaultValue" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['defaultValue'],
            "options" => [],
        ];
        
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->patch($uri, $this->consultationFeedbackFormInput, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
        
        $selectFieldEntry = [
            "name" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['mandatory'],
        ];
        $this->seeInDatabase("SelectField", $selectFieldEntry);
        
        $singleSelectFieldEntry = [
            "defaultValue" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['defaultValue'],
            "removed" => false,
        ];
        $this->seeInDatabase('SingleSelectField', $singleSelectFieldEntry);
    }
    public function test_update_fieldInputWithId_updateExistingFieldWithSameId()
    {
        $this->consultationFeedbackFormInput['singleSelectFields'][0]['id'] = $this->singleSelectField->id;
        $response = [
            "id" => $this->consultationFeedbackForm->id,
            "name" => $this->consultationFeedbackFormInput['name'],
            "description" => $this->consultationFeedbackFormInput['description'],
            "singleSelectFields" => [
                [
                    "id" => $this->singleSelectField->id,
                    "name" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['name'],
                    "description" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['description'],
                    "position" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['position'],
                    "mandatory" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['mandatory'],
                    "defaultValue" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['defaultValue'],
                    "options" => [],
                ],
            ],
        ];
        
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->patch($uri, $this->consultationFeedbackFormInput, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
        
        $selectFieldEntry = [
            "id" => $this->singleSelectField->selectField->id,
            "name" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['mandatory'],
        ];
        $this->seeInDatabase("SelectField", $selectFieldEntry);
        
        $singleSelectFieldEntry = [
            "Form_id" => $this->consultationFeedbackForm->id,
            "id" => $this->singleSelectField->id,
            "defaultValue" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['defaultValue'],
            "removed" => false,
        ];
        $this->seeInDatabase('SingleSelectField', $singleSelectFieldEntry);
    }
    public function test_update_existingFieldNotMentionInUpdateInput_removeThisField()
    {
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->patch($uri, $this->consultationFeedbackFormInput, $this->manager->token)
            ->seeStatusCode(200);
        
        $singleSelectFieldEntry = [
            "Form_id" => $this->consultationFeedbackForm->id,
            "id" => $this->singleSelectField->id,
            "removed" => true,
        ];
        $this->seeInDatabase('SingleSelectField', $singleSelectFieldEntry);
    }
    public function test_update_fieldInputWithIdNotFoundIndRecord_addAsNewField()
    {
        $this->consultationFeedbackFormInput['singleSelectFields'][0]['id'] = 'not existing id';
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->patch($uri, $this->consultationFeedbackFormInput, $this->manager->token)
            ->seeStatusCode(200);
        
        $removedSingleSelectField = [
            "Form_id" => $this->consultationFeedbackForm->id,
            "id" => $this->singleSelectField->id,
            "removed" => true,
        ];
        $this->seeInDatabase('SingleSelectField', $removedSingleSelectField);
        
        $selectFieldEntry = [
            "name" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['name'],
            "description" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['description'],
            "position" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['position'],
            "mandatory" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['mandatory'],
        ];
        $this->seeInDatabase("SelectField", $selectFieldEntry);
        
        $singleSelectFieldEntry = [
            "defaultValue" => $this->consultationFeedbackFormInput['singleSelectFields'][0]['defaultValue'],
            "removed" => false,
        ];
        $this->seeInDatabase('SingleSelectField', $singleSelectFieldEntry);
    }
    public function test_show()
    {
        $response = [
            "id" => $this->consultationFeedbackForm->id,
            "name" => $this->consultationFeedbackForm->form->name,
            "description" => $this->consultationFeedbackForm->form->description,
            "singleSelectFields" => [
                [
                    "id" => $this->singleSelectField->id,
                    "name" => $this->singleSelectField->selectField->name,
                    "description" => $this->singleSelectField->selectField->description,
                    "position" => $this->singleSelectField->selectField->position,
                    "mandatory" => $this->singleSelectField->selectField->mandatory,
                    "defaultValue" => $this->singleSelectField->defaultValue,
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
