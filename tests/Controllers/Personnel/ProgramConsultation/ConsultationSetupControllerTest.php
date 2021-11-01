<?php

namespace Tests\Controllers\Personnel\ProgramConsultation;

use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultationSetup;
use Tests\Controllers\RecordPreparation\Firm\RecordOfFeedbackForm;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfStringField;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;

class ConsultationSetupControllerTest extends ProgramConsultationTestCase
{
    protected $feedbackFormOne;
    protected $stringFieldOne;

    protected $consultationSetupOne;
    protected $consultationSetupTwo;
    
    protected $consultationSetupUri;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Form')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('IntegerField')->truncate();
        $this->connection->table('StringField')->truncate();
        $this->connection->table('TextAreaField')->truncate();
        $this->connection->table('AttachmentField')->truncate();
        $this->connection->table('SingleSelectField')->truncate();
        $this->connection->table('MultiSelectField')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        
        $this->consultationSetupUri = $this->programConsultationUri . "/consultation-setups";
        
        $program = $this->programConsultation->program;
        $firm = $program->firm;
        
        $form = new RecordOfForm('1');
        
        $this->feedbackFormOne = new RecordOfFeedbackForm($firm, $form);
        
        $this->stringFieldOne = new RecordOfStringField($form, '1');
        
        $this->consultationSetupOne = new RecordOfConsultationSetup($program, null, $this->feedbackFormOne, '1');
        $this->consultationSetupTwo = new RecordOfConsultationSetup($program, null, $this->feedbackFormOne, '2');
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Form')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('IntegerField')->truncate();
        $this->connection->table('StringField')->truncate();
        $this->connection->table('TextAreaField')->truncate();
        $this->connection->table('AttachmentField')->truncate();
        $this->connection->table('SingleSelectField')->truncate();
        $this->connection->table('MultiSelectField')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
    }
    
    protected function show()
    {
        $this->feedbackFormOne->insert($this->connection);
        $this->stringFieldOne->insert($this->connection);
        
        $this->consultationSetupOne->insert($this->connection);
        $uri = $this->consultationSetupUri . "/{$this->consultationSetupOne->id}";
        $this->get($uri, $this->programConsultation->personnel->token);
    }
    public function test_show_200()
    {
        $this->show();
        $this->seeStatusCode(200);
        
        $result = [
            'id' => $this->consultationSetupOne->id,
            'name' => $this->consultationSetupOne->name,
            'duration' => $this->consultationSetupOne->sessionDuration,
            'feedbackForm' => [
                'id' => $this->consultationSetupOne->consultantFeedbackForm->id,
                'name' => $this->consultationSetupOne->consultantFeedbackForm->form->name,
                'description' => $this->consultationSetupOne->consultantFeedbackForm->form->description,
                'stringFields' => [
                    [
                        'id' => $this->stringFieldOne->id,
                        'name' => $this->stringFieldOne->name,
                        'description' => $this->stringFieldOne->description,
                        'position' => $this->stringFieldOne->position,
                        'mandatory' => $this->stringFieldOne->mandatory,
                        'defaultValue' => $this->stringFieldOne->defaultValue,
                        'minValue' => $this->stringFieldOne->minValue,
                        'maxValue' => $this->stringFieldOne->maxValue,
                        'placeholder' => $this->stringFieldOne->placeholder,
                    ],
                ],
                'integerFields' => [],
                'textAreaFields' => [],
                'attachmentFields' => [],
                'singleSelectFields' => [],
                'multiSelectFields' => [],
            ],
        ];
        $this->seeJsonContains($result);
    }
    public function test_show_inactiveConsultant_403()
    {
        $this->connection->table('Consultant')->truncate();
        $this->programConsultation->active = false;
        $this->programConsultation->insert($this->connection);
        
        $this->show();
        $this->seeStatusCode(403);
    }
    
    protected function showAll()
    {
        $this->consultationSetupOne->insert($this->connection);
        $this->consultationSetupTwo->insert($this->connection);
        
        $this->get($this->consultationSetupUri, $this->programConsultation->personnel->token);
    }
    public function test_showAll_200()
    {
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 2];
        $this->seeJsonContains($totalResponse);
        
        $consultationSetupOneResponse = [
            'id' => $this->consultationSetupOne->id,
            'name' => $this->consultationSetupOne->name,
            'duration' => $this->consultationSetupOne->sessionDuration,
        ];
        $this->seeJsonContains($consultationSetupOneResponse);
        
        $consultationSetupTwoResponse = [
            'id' => $this->consultationSetupTwo->id,
            'name' => $this->consultationSetupTwo->name,
            'duration' => $this->consultationSetupTwo->sessionDuration,
        ];
        $this->seeJsonContains($consultationSetupTwoResponse);
    }
    public function test_showAll_paginationSet()
    {
        $this->consultationSetupUri .= "?page=1&pageSize=1";
        $this->showAll();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => 2,
            'list' => [
                [
                    'id' => $this->consultationSetupOne->id,
                    'name' => $this->consultationSetupOne->name,
                    'duration' => $this->consultationSetupOne->sessionDuration,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_inactiveConsultant_403()
    {
        $this->connection->table('Consultant')->truncate();
        $this->programConsultation->active = false;
        $this->programConsultation->insert($this->connection);
        
        $this->showAll();
        $this->seeStatusCode(403);
    }
}
