<?php

namespace Tests\Controllers\Admin;

use Tests\Controllers\RecordPreparation\Firm\RecordOfWorksheetForm;
use Tests\Controllers\RecordPreparation\RecordOfFirm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;

class WorksheetFormControllerTest extends AdminTestCase
{
    protected $worksheetFormUri;
    protected $worksheetFormOne;
    protected $worksheetFormTwo;
    protected $firm;
    protected $formDataRequest = [
        'name' => 'new form name',
        'description' => 'new form description',
        'stringFields' => [],
        'integerFields' => [],
        'textAreaFields' => [],
        'attachmentFields' => [],
        'singleSelectFields' => [],
        'multiSelectFields' => [],
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->worksheetFormUri = $this->adminUri . '/worksheet-forms';
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('WorksheetForm')->truncate();
        
        $this->firm = new RecordOfFirm('99');
        
        $formOne = new RecordOfForm('1');
        $formTwo = new RecordOfForm('2');
        
        $this->worksheetFormOne = new RecordOfWorksheetForm(null, $formOne);
        $this->worksheetFormTwo = new RecordOfWorksheetForm(null, $formTwo);
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('WorksheetForm')->truncate();
    }
    
    protected function executeCreate()
    {
        $this->post($this->worksheetFormUri, $this->formDataRequest, $this->admin->token);
    }
    public function test_create_201()
    {
        $this->executeCreate();
        $this->seeStatusCode(201);
        $response = [
            'name' => $this->formDataRequest['name'],
            'description' => $this->formDataRequest['description'],
            'stringFields' => [],
            'integerFields' => [],
            'textAreaFields' => [],
            'attachmentFields' => [],
            'singleSelectFields' => [],
            'multiSelectFields' => [],
        ];
        $this->seeJsonContains($response);
        
        $formEntry = [
            'name' => $this->formDataRequest['name'],
            'description' => $this->formDataRequest['description'],
        ];
        $this->seeInDatabase('Form', $formEntry);
        
        $worksheetFormEntry = [
            'Firm_id' => null,
            'removed' => false,
        ];
        $this->seeInDatabase('WorksheetForm', $worksheetFormEntry);
    }
    
    protected function executeUpdate()
    {
        $this->worksheetFormOne->form->insert($this->connection);
        $this->worksheetFormOne->insert($this->connection);
        $uri = $this->worksheetFormUri . "/{$this->worksheetFormOne->id}";
        $this->patch($uri, $this->formDataRequest, $this->admin->token);
    }
    public function test_update_200()
    {
        $this->executeUpdate();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->worksheetFormOne->id,
            'name' => $this->formDataRequest['name'],
            'description' => $this->formDataRequest['description'],
            'stringFields' => [],
            'integerFields' => [],
            'textAreaFields' => [],
            'attachmentFields' => [],
            'singleSelectFields' => [],
            'multiSelectFields' => [],
        ];
        $this->seeJsonContains($response);
        
        $formEntry = [
            'id' => $this->worksheetFormOne->id,
            'name' => $this->formDataRequest['name'],
            'description' => $this->formDataRequest['description'],
        ];
        $this->seeInDatabase('Form', $formEntry);
    }
    public function test_update_notGlobalWorksheetForm_403()
    {
        $this->worksheetFormOne->firm = $this->firm;
        $this->worksheetFormOne->firm->insert($this->connection);
        $this->executeUpdate();
        $this->seeStatusCode(403);
    }
    
    protected function executeRemove()
    {
        $this->worksheetFormOne->form->insert($this->connection);
        $this->worksheetFormOne->insert($this->connection);
        $uri = $this->worksheetFormUri . "/{$this->worksheetFormOne->id}";
        $this->delete($uri, [], $this->admin->token);
    }
    public function test_remove_200()
    {
        $this->executeRemove();
        $this->seeStatusCode(200);
        $worksheetFormEntry = [
            'id' => $this->worksheetFormOne->id,
            'removed' => true,
        ];
        $this->seeInDatabase('WorksheetForm', $worksheetFormEntry);
    }
    public function test_remove_notGlobalWorksheetForm_403()
    {
        $this->worksheetFormOne->firm = $this->firm;
        $this->worksheetFormOne->firm->insert($this->connection);
        $this->executeRemove();
        $this->seeStatusCode(403);
    }
    
    protected function executeShow()
    {
        $this->worksheetFormOne->form->insert($this->connection);
        $this->worksheetFormOne->insert($this->connection);
        $uri = $this->worksheetFormUri . "/{$this->worksheetFormOne->id}";
        $this->get($uri, $this->admin->token);
    }
    public function test_show_200()
    {
        $this->executeShow();
        $this->seeStatusCode(200);
        $response = [
            'id' => $this->worksheetFormOne->id,
            'name' => $this->worksheetFormOne->form->name,
            'description' => $this->worksheetFormOne->form->description,
            'stringFields' => [],
            'integerFields' => [],
            'textAreaFields' => [],
            'attachmentFields' => [],
            'singleSelectFields' => [],
            'multiSelectFields' => [],
        ];
        $this->seeJsonContains($response);
    }
    public function test_show_notGlobalForm_404()
    {
        $this->worksheetFormOne->firm = $this->firm;
        $this->worksheetFormOne->firm->insert($this->connection);
        $this->executeShow();
        $this->seeStatusCode(404);
    }
    
    protected function executeShowAll()
    {
        $this->worksheetFormOne->form->insert($this->connection);
        $this->worksheetFormOne->insert($this->connection);
        $this->worksheetFormTwo->form->insert($this->connection);
        $this->worksheetFormTwo->insert($this->connection);
        $this->get($this->worksheetFormUri, $this->admin->token);
    }
    public function test_showAll_200()
    {
        $this->executeShowAll();
        $this->seeStatusCode(200);
        $totalResponse = ['total' => 2];
        $worksheetOneRespose = [
            'id' => $this->worksheetFormOne->id,
            'name' => $this->worksheetFormOne->form->name,
        ];
        $this->seeJsonContains($worksheetOneRespose);
        $worksheetTwoRespose = [
            'id' => $this->worksheetFormTwo->id,
            'name' => $this->worksheetFormTwo->form->name,
        ];
        $this->seeJsonContains($worksheetTwoRespose);
    }
    public function test_show_containFirmForm_excludeFromResult()
    {
        $this->worksheetFormOne->firm = $this->firm;
        $this->worksheetFormOne->firm->insert($this->connection);
        $this->executeShowAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 1];
        $worksheetTwoRespose = [
            'id' => $this->worksheetFormTwo->id,
            'name' => $this->worksheetFormTwo->form->name,
        ];
        $this->seeJsonContains($worksheetTwoRespose);
    }
}
