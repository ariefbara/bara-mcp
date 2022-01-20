<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\RecordPreparation\ {
    Firm\RecordOfWorksheetForm,
    Shared\RecordOfForm
};

class WorksheetFormControllerTest extends ManagerTestCase
{
    protected $worksheetFormUri;
    /**
     *
     * @var RecordOfWorksheetForm
     */
    protected $worksheetForm;
    protected $worksheetFormOne;
    protected $globalWorksheetForm;
    protected $worksheetFormInput = [
        "name" => 'new worksheet form name',
        "description" => 'new worksheet form description',
        "stringFields" => [],
        "integerFields" => [],
        "textAreaFields" => [],
        "singleSelectFields" => [],
        "multiSelectFields" => [],
        "attachmentFields" => [],
        "sections" => [],
    ];
    protected $worksheetFormResponse = [
        "stringFields" => [],
        "integerFields" => [],
        "textAreaFields" => [],
        "singleSelectFields" => [],
        "multiSelectFields" => [],
        "attachmentFields" => [],
        "sections" => [],
    ];
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->worksheetFormUri = $this->managerUri . "/worksheet-forms";
        $this->connection->table("WorksheetForm")->truncate();
        $this->connection->table("Form")->truncate();
        
        $this->connection->table('StringField')->truncate();
        $this->connection->table('IntegerField')->truncate();
        $this->connection->table('TextAreaField')->truncate();
        $this->connection->table('AttachmentField')->truncate();
        $this->connection->table('SingleSelectField')->truncate();
        $this->connection->table('MultiSelectField')->truncate();
        $this->connection->table('Section')->truncate();
        
        $this->connection->table('SelectField')->truncate();
        $this->connection->table('T_Option')->truncate();
        
        $form = new RecordOfForm(0);
        $formOne = new RecordOfForm(1);
        $formTwo = new RecordOfForm(2);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());
        $this->connection->table("Form")->insert($formOne->toArrayForDbEntry());
        $this->connection->table("Form")->insert($formTwo->toArrayForDbEntry());
        
        $this->worksheetForm = new RecordOfWorksheetForm($this->firm, $form);
        $this->worksheetFormOne = new RecordOfWorksheetForm($this->firm, $formOne);
        $this->globalWorksheetForm = new RecordOfWorksheetForm(null, $formTwo);
        $this->connection->table("WorksheetForm")->insert($this->worksheetForm->toArrayForDbEntry());
        $this->connection->table("WorksheetForm")->insert($this->worksheetFormOne->toArrayForDbEntry());
        $this->connection->table("WorksheetForm")->insert($this->globalWorksheetForm->toArrayForDbEntry());
        
        $this->worksheetFormResponse["name"] = $this->worksheetFormInput["name"];
        $this->worksheetFormResponse["description"] = $this->worksheetFormInput["description"];
        
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("WorksheetForm")->truncate();
        $this->connection->table("Form")->truncate();
        
        $this->connection->table('StringField')->truncate();
        $this->connection->table('IntegerField')->truncate();
        $this->connection->table('TextAreaField')->truncate();
        $this->connection->table('AttachmentField')->truncate();
        $this->connection->table('SingleSelectField')->truncate();
        $this->connection->table('MultiSelectField')->truncate();
        $this->connection->table('Section')->truncate();
        
        $this->connection->table('SelectField')->truncate();
        $this->connection->table('T_Option')->truncate();
    }
    
    public function test_add()
    {
        $this->connection->table("Form")->truncate();
        $this->connection->table("WorksheetForm")->truncate();
        
        $response = [
            "name" => $this->worksheetFormInput["name"],
            "description" => $this->worksheetFormInput["description"],
            "stringFields" => [],
            "integerFields" => [],
            "textAreaFields" => [],
            "attachmentFields" => [],
            "singleSelectFields" => [],
            "multiSelectFields" => [],
            "sections" => [],
        ];
        $this->post($this->worksheetFormUri, $this->worksheetFormInput, $this->manager->token)
                ->seeStatusCode(201)
                ->seeJsonContains($response);
        
        $formEntry = [
            "name" => $this->worksheetFormInput["name"],
            "description" => $this->worksheetFormInput["description"],
        ];
        $this->seeInDatabase("Form", $formEntry);
        
        $worksheetFormEntry = [
            "Firm_id" => $this->firm->id,
            "removed" => false,
        ];
        $this->seeInDatabase("WorksheetForm", $worksheetFormEntry);
    }
    public function test_add_userNotManager_error401()
    {
        $this->post($this->worksheetFormUri, $this->worksheetFormInput, $this->removedManager->token)
                ->seeStatusCode(401);
    }
    public function test_add_emptyName_error400()
    {
        $this->worksheetFormInput['name'] = '';
        $this->post($this->worksheetFormUri, $this->worksheetFormInput, $this->manager->token)
                ->seeStatusCode(400);
    }
    
    public function test_update()
    {
        $response = [
            "name" => $this->worksheetFormInput["name"],
            "description" => $this->worksheetFormInput["description"],
            "stringFields" => [],
            "integerFields" => [],
            "textAreaFields" => [],
            "attachmentFields" => [],
            "singleSelectFields" => [],
            "multiSelectFields" => [],
            "sections" => [],
        ];
        $uri = $this->worksheetFormUri . "/{$this->worksheetForm->id}";
        $this->patch($uri, $this->worksheetFormInput, $this->manager->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
        
        $formEntry = [
            "id" => $this->worksheetForm->form->id,
            "name" => $this->worksheetFormInput["name"],
            "description" => $this->worksheetFormInput["description"],
        ];
        $this->seeInDatabase("Form", $formEntry);
    }
    public function test_update_emptyName_error400()
    {
        $this->worksheetFormInput['name'] = '';
        $uri = $this->worksheetFormUri . "/{$this->worksheetForm->id}";
        $this->patch($uri, $this->worksheetFormInput, $this->manager->token)
                ->seeStatusCode(400);
    }
    public function test_update_userNotManager_error401()
    {
        $uri = $this->worksheetFormUri . "/{$this->worksheetForm->id}";
        $this->patch($uri, $this->worksheetFormInput, $this->removedManager->token)
                ->seeStatusCode(401);
    }
    
    public function test_remove()
    {
        $uri = $this->worksheetFormUri . "/{$this->worksheetForm->id}";
        $this->delete($uri, [], $this->manager->token)
                ->seeStatusCode(200);
        
        $worksheetFormEntry = [
            "Firm_id" => $this->firm->id,
            "id" => $this->worksheetForm->id,
            "removed" => true,
        ];
        $this->seeInDatabase("WorksheetForm", $worksheetFormEntry);
    }
    public function test_remove_userNotManager_error401()
    {
        $uri = $this->worksheetFormUri . "/{$this->worksheetForm->id}";
        $this->delete($uri, [], $this->removedManager->token)
                ->seeStatusCode(401);
    }
    
    public function test_show()
    {
        $response = [
            "id" => $this->worksheetForm->id,
            "name" => $this->worksheetForm->form->name,
            "description" => $this->worksheetForm->form->description,
            "stringFields" => [],
            "integerFields" => [],
            "textAreaFields" => [],
            "attachmentFields" => [],
            "singleSelectFields" => [],
            "multiSelectFields" => [],
            "sections" => [],
        ];
        $uri = $this->worksheetFormUri . "/{$this->worksheetForm->id}";
        $this->get($uri, $this->manager->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_show_globalWorksheetForm_200()
    {
        $response = [
            "id" => $this->globalWorksheetForm->id,
            "name" => $this->globalWorksheetForm->form->name,
            "description" => $this->globalWorksheetForm->form->description,
            "stringFields" => [],
            "integerFields" => [],
            "textAreaFields" => [],
            "attachmentFields" => [],
            "singleSelectFields" => [],
            "multiSelectFields" => [],
            "sections" => [],
        ];
        $uri = $this->worksheetFormUri . "/{$this->globalWorksheetForm->id}";
        $this->get($uri, $this->manager->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_show_userNotManager_error401()
    {
        $uri = $this->worksheetFormUri . "/{$this->worksheetForm->id}";
        $this->get($uri, $this->removedManager->token)
                ->seeStatusCode(401);
    }
    
    public function test_showAll()
    {
        $response = [
            "total" => 3, 
            "list" => [
                [
                    "id" => $this->worksheetForm->id,
                    "name" => $this->worksheetForm->form->name,
                    'globalForm' => false,
                ],
                [
                    "id" => $this->worksheetFormOne->id,
                    "name" => $this->worksheetFormOne->form->name,
                    'globalForm' => false,
                ],
                [
                    "id" => $this->globalWorksheetForm->id,
                    "name" => $this->globalWorksheetForm->form->name,
                    'globalForm' => true,
                ],
            ],
        ];
        $this->get($this->worksheetFormUri, $this->manager->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_showAll_userNotManager_error401()
    {
        $this->get($this->worksheetFormUri, $this->removedManager->token)
                ->seeStatusCode(401);
    }
}
