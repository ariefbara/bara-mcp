<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\RecordPreparation\ {
    Firm\RecordOfConsultationFeedbackForm,
    Shared\RecordOfForm
};

class ConsultationFeedbackFormControllerTest extends ConsultationFeedbackFormTestCase
{
    protected $consultationFeedbackFormOne;
    
    protected function setUp(): void
    {
        parent::setUp();
        $form = new RecordOfForm(1);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());
        
        $this->consultationFeedbackFormOne = new RecordOfConsultationFeedbackForm($this->firm, $form);
        $this->connection->table("ConsultationFeedbackForm")->insert($this->consultationFeedbackFormOne->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    public function test_add()
    {
        $this->connection->table("Form")->truncate();
        $this->connection->table("ConsultationFeedbackForm")->truncate();
        
        $response = [
            "name" => $this->consultationFeedbackFormInput["name"],
            "description" => $this->consultationFeedbackFormInput["description"],
            "stringFields" => [],
            "integerFields" => [],
            "textAreaFields" => [],
            "attachmentFields" => [],
            "singleSelectFields" => [],
            "multiSelectFields" => [],
        ];
        $this->post($this->consultationFeedbackFormUri, $this->consultationFeedbackFormInput, $this->manager->token)
                ->seeStatusCode(201)
                ->seeJsonContains($response);
        
        $formEntry = [
            "name" => $this->consultationFeedbackFormInput["name"],
            "description" => $this->consultationFeedbackFormInput["description"],
        ];
        $this->seeInDatabase("Form", $formEntry);
        
        $consultationFeedbackFormEntry = [
            "Firm_id" => $this->firm->id,
            "removed" => false,
        ];
        $this->seeInDatabase("ConsultationFeedbackForm", $consultationFeedbackFormEntry);
    }
    public function test_add_userNotManager_error401()
    {
        $this->post($this->consultationFeedbackFormUri, $this->consultationFeedbackFormInput, $this->removedManager->token)
                ->seeStatusCode(401);
    }
    public function test_add_emptyName_error400()
    {
        $this->consultationFeedbackFormInput['name'] = '';
        $this->post($this->consultationFeedbackFormUri, $this->consultationFeedbackFormInput, $this->manager->token)
                ->seeStatusCode(400);
    }
    
    public function test_update()
    {
        $response = [
            "name" => $this->consultationFeedbackFormInput["name"],
            "description" => $this->consultationFeedbackFormInput["description"],
            "stringFields" => [],
            "integerFields" => [],
            "textAreaFields" => [],
            "attachmentFields" => [],
            "singleSelectFields" => [],
            "multiSelectFields" => [],
        ];
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->patch($uri, $this->consultationFeedbackFormInput, $this->manager->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
        
        $formEntry = [
            "id" => $this->consultationFeedbackForm->form->id,
            "name" => $this->consultationFeedbackFormInput["name"],
            "description" => $this->consultationFeedbackFormInput["description"],
        ];
        $this->seeInDatabase("Form", $formEntry);
    }
    public function test_update_emptyName_error400()
    {
        $this->consultationFeedbackFormInput['name'] = '';
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->patch($uri, $this->consultationFeedbackFormInput, $this->manager->token)
                ->seeStatusCode(400);
    }
    public function test_update_userNotManager_error401()
    {
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->patch($uri, $this->consultationFeedbackFormInput, $this->removedManager->token)
                ->seeStatusCode(401);
    }
    
    public function test_remove()
    {
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->delete($uri, [], $this->manager->token)
                ->seeStatusCode(200);
        
        $consultationFeedbackFormEntry = [
            "Firm_id" => $this->firm->id,
            "id" => $this->consultationFeedbackForm->id,
            "removed" => true,
        ];
        $this->seeInDatabase("ConsultationFeedbackForm", $consultationFeedbackFormEntry);
    }
    public function test_remove_userNotManager_error401()
    {
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->delete($uri, [], $this->removedManager->token)
                ->seeStatusCode(401);
    }
    
    public function test_show()
    {
        $response = [
            "id" => $this->consultationFeedbackForm->id,
            "name" => $this->consultationFeedbackForm->form->name,
            "description" => $this->consultationFeedbackForm->form->description,
            "stringFields" => [],
            "integerFields" => [],
            "textAreaFields" => [],
            "attachmentFields" => [],
            "singleSelectFields" => [],
            "multiSelectFields" => [],
        ];
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->get($uri, $this->manager->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_show_userNotManager_error401()
    {
        $uri = $this->consultationFeedbackFormUri . "/{$this->consultationFeedbackForm->id}";
        $this->get($uri, $this->removedManager->token)
                ->seeStatusCode(401);
    }
    
    public function test_showAll()
    {
$this->disableExceptionHandling();
        $response = [
            "total" => 2, 
            "list" => [
                [
                    "id" => $this->consultationFeedbackForm->id,
                    "name" => $this->consultationFeedbackForm->form->name,
                ],
                [
                    "id" => $this->consultationFeedbackFormOne->id,
                    "name" => $this->consultationFeedbackFormOne->form->name,
                ],
            ],
        ];
        $this->get($this->consultationFeedbackFormUri, $this->manager->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_showAll_userNotManager_error401()
    {
        $this->get($this->consultationFeedbackFormUri, $this->removedManager->token)
                ->seeStatusCode(401);
    }
}
