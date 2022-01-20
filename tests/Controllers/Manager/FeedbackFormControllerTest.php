<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\RecordPreparation\ {
    Firm\RecordOfFeedbackForm,
    Shared\RecordOfForm
};

class FeedbackFormControllerTest extends FeedbackFormTestCase
{
    protected $feedbackFormOne;
    
    protected function setUp(): void
    {
        parent::setUp();
        $form = new RecordOfForm(1);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());
        
        $this->feedbackFormOne = new RecordOfFeedbackForm($this->firm, $form);
        $this->connection->table("FeedbackForm")->insert($this->feedbackFormOne->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    public function test_add()
    {
$this->disableExceptionHandling();
        $this->connection->table("Form")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        
        $response = [
            "name" => $this->feedbackFormInput["name"],
            "description" => $this->feedbackFormInput["description"],
            "stringFields" => [],
            "integerFields" => [],
            "textAreaFields" => [],
            "attachmentFields" => [],
            "singleSelectFields" => [],
            "multiSelectFields" => [],
            "sections" => [],
        ];
        $this->post($this->feedbackFormUri, $this->feedbackFormInput, $this->manager->token)
                ->seeStatusCode(201)
                ->seeJsonContains($response);
        
        $formEntry = [
            "name" => $this->feedbackFormInput["name"],
            "description" => $this->feedbackFormInput["description"],
        ];
        $this->seeInDatabase("Form", $formEntry);
        
        $feedbackFormEntry = [
            "Firm_id" => $this->firm->id,
            "removed" => false,
        ];
        $this->seeInDatabase("FeedbackForm", $feedbackFormEntry);
    }
    public function test_add_userNotManager_error401()
    {
        $this->post($this->feedbackFormUri, $this->feedbackFormInput, $this->removedManager->token)
                ->seeStatusCode(401);
    }
    public function test_add_emptyName_error400()
    {
        $this->feedbackFormInput['name'] = '';
        $this->post($this->feedbackFormUri, $this->feedbackFormInput, $this->manager->token)
                ->seeStatusCode(400);
    }
    
    public function test_update()
    {
        $response = [
            "name" => $this->feedbackFormInput["name"],
            "description" => $this->feedbackFormInput["description"],
            "stringFields" => [],
            "integerFields" => [],
            "textAreaFields" => [],
            "attachmentFields" => [],
            "singleSelectFields" => [],
            "multiSelectFields" => [],
            "sections" => [],
        ];
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->patch($uri, $this->feedbackFormInput, $this->manager->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
        
        $formEntry = [
            "id" => $this->feedbackForm->form->id,
            "name" => $this->feedbackFormInput["name"],
            "description" => $this->feedbackFormInput["description"],
        ];
        $this->seeInDatabase("Form", $formEntry);
    }
    public function test_update_emptyName_error400()
    {
        $this->feedbackFormInput['name'] = '';
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->patch($uri, $this->feedbackFormInput, $this->manager->token)
                ->seeStatusCode(400);
    }
    public function test_update_userNotManager_error401()
    {
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->patch($uri, $this->feedbackFormInput, $this->removedManager->token)
                ->seeStatusCode(401);
    }
    
    public function test_remove()
    {
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->delete($uri, [], $this->manager->token)
                ->seeStatusCode(200);
        
        $feedbackFormEntry = [
            "Firm_id" => $this->firm->id,
            "id" => $this->feedbackForm->id,
            "removed" => true,
        ];
        $this->seeInDatabase("FeedbackForm", $feedbackFormEntry);
    }
    public function test_remove_userNotManager_error401()
    {
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->delete($uri, [], $this->removedManager->token)
                ->seeStatusCode(401);
    }
    
    public function test_show()
    {
        $response = [
            "id" => $this->feedbackForm->id,
            "name" => $this->feedbackForm->form->name,
            "description" => $this->feedbackForm->form->description,
            "stringFields" => [],
            "integerFields" => [],
            "textAreaFields" => [],
            "attachmentFields" => [],
            "singleSelectFields" => [],
            "multiSelectFields" => [],
            "sections" => [],
        ];
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->get($uri, $this->manager->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_show_userNotManager_error401()
    {
        $uri = $this->feedbackFormUri . "/{$this->feedbackForm->id}";
        $this->get($uri, $this->removedManager->token)
                ->seeStatusCode(401);
    }
    
    public function test_showAll()
    {
        $response = [
            "total" => 2, 
            "list" => [
                [
                    "id" => $this->feedbackForm->id,
                    "name" => $this->feedbackForm->form->name,
                ],
                [
                    "id" => $this->feedbackFormOne->id,
                    "name" => $this->feedbackFormOne->form->name,
                ],
            ],
        ];
        $this->get($this->feedbackFormUri, $this->manager->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_showAll_userNotManager_error401()
    {
        $this->get($this->feedbackFormUri, $this->removedManager->token)
                ->seeStatusCode(401);
    }
}
