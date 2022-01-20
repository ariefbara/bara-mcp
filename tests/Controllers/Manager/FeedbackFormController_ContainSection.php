<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfSection;

class FeedbackFormController_ContainSection extends FeedbackFormTestCase
{
    protected $section;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->section = new RecordOfSection($this->feedbackForm->form, 0);
        $this->connection->table('Section')->insert($this->section->toArrayForDbEntry());
        
        $this->feedbackFormInput['sections'][] = [
            "name" => "new text area section name",
            "position" => "new text area section position",
        ];
    }
    
    public function test_add()
    {
        $response = [
            "name" => $this->feedbackFormInput['sections'][0]['name'],
            "position" => $this->feedbackFormInput['sections'][0]['position'],
        ];
        
        $this->post($this->feedbackFormUri, $this->feedbackFormInput, $this->manager->token)
            ->seeStatusCode(201)
            ->seeJsonContains($response);
        
        $sectionEntry = [
            "name" => $this->feedbackFormInput['sections'][0]['name'],
            "position" => $this->feedbackFormInput['sections'][0]['position'],
            "removed" => false,
        ];
        $this->seeInDatabase('Section', $sectionEntry);
    }
    public function test_update_sectionInputWithoutId_addAsNewSection()
    {
        $response = [
            "name" => $this->feedbackFormInput['sections'][0]['name'],
            "position" => $this->feedbackFormInput['sections'][0]['position'],
        ];
        
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->patch($uri, $this->feedbackFormInput, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
        
        $sectionEntry = [
            "Form_id" => $this->feedbackForm->id,
            "name" => $this->feedbackFormInput['sections'][0]['name'],
            "position" => $this->feedbackFormInput['sections'][0]['position'],
            "removed" => false,
        ];
        $this->seeInDatabase('Section', $sectionEntry);
    }
    public function test_update_sectionInputWithId_updateExistingSectionWithSameId()
    {
        $this->feedbackFormInput['sections'][0]['id'] = $this->section->id;
        $response = [
            "id" => $this->feedbackForm->id,
            "name" => $this->feedbackFormInput['name'],
            "description" => $this->feedbackFormInput['description'],
            "sections" => [
                [
                    "id" => $this->section->id,
                    "name" => $this->feedbackFormInput['sections'][0]['name'],
                    "position" => $this->feedbackFormInput['sections'][0]['position'],
                ],
            ],
        ];
        
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->patch($uri, $this->feedbackFormInput, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
        
        $sectionEntry = [
            "Form_id" => $this->feedbackForm->id,
            "id" => $this->section->id,
            "name" => $this->feedbackFormInput['sections'][0]['name'],
            "position" => $this->feedbackFormInput['sections'][0]['position'],
            "removed" => false,
        ];
        $this->seeInDatabase('Section', $sectionEntry);
    }
    public function test_update_existingSectionNotMentionInUpdateInput_removeThisSection()
    {
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->patch($uri, $this->feedbackFormInput, $this->manager->token)
            ->seeStatusCode(200);
        
        $sectionEntry = [
            "Form_id" => $this->feedbackForm->id,
            "id" => $this->section->id,
            "removed" => true,
        ];
        $this->seeInDatabase('Section', $sectionEntry);
    }
    public function test_update_sectionInputWithIdNotFoundIndRecord_addAsNewSection()
    {
        $this->feedbackFormInput['sections'][0]['id'] = 'not existing id';
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->patch($uri, $this->feedbackFormInput, $this->manager->token)
            ->seeStatusCode(200);
        
        $removedSection = [
            "Form_id" => $this->feedbackForm->id,
            "id" => $this->section->id,
            "removed" => true,
        ];
        $this->seeInDatabase('Section', $removedSection);
        
        $newSectionRecord = [
            "Form_id" => $this->feedbackForm->id,
            "name" => $this->feedbackFormInput['sections'][0]['name'],
            "position" => $this->feedbackFormInput['sections'][0]['position'],
            "removed" => false,
        ];
        $this->seeInDatabase('Section', $newSectionRecord);
    }
    public function test_show()
    {
        $response = [
            "id" => $this->feedbackForm->id,
            "name" => $this->feedbackForm->form->name,
            "description" => $this->feedbackForm->form->description,
            "sections" => [
                [
                    "id" => $this->section->id,
                    "name" => $this->section->name,
                    "position" => $this->section->position,
                ],
            ],
        ];
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->get($uri, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
}
