<?php

namespace Tests\Controllers\Client\AsProgramParticipant;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\RecordOfConsultationSetup,
    Firm\RecordOfFeedbackForm,
    Shared\RecordOfForm
};

class ConsultationSetupControllerTest extends AsProgramParticipantTestCase
{
    protected $consultationSetupUri;
    protected $consultationSetup;
    protected $consultationSetupOne;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSetupUri = $this->asProgramparticipantUri . "/consultation-setups";
        $this->connection->table('Form')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        
        $form = new RecordOfForm(0);
        $this->connection->table('Form')->insert($form->toArrayForDbEntry());
        
        $feedbackForm = new RecordOfFeedbackForm($this->participant->program->firm, $form);
        $this->connection->table('FeedbackForm')->insert($feedbackForm->toArrayForDbEntry());
        
        $this->consultationSetup = new RecordOfConsultationSetup($this->participant->program, $feedbackForm, $feedbackForm, 0);
        $this->consultationSetupOne = new RecordOfConsultationSetup($this->participant->program, $feedbackForm, $feedbackForm, 1);
        $this->connection->table('ConsultationSetup')->insert($this->consultationSetup->toArrayForDbEntry());
        $this->connection->table('ConsultationSetup')->insert($this->consultationSetupOne->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Form')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
    }
    
    public function test_show()
    {
        $response = [
            "id" => $this->consultationSetup->id,
            "name" => $this->consultationSetup->name,
            "sessionDuration" => $this->consultationSetup->sessionDuration,
        ];
        $uri = $this->consultationSetupUri . "/{$this->consultationSetup->id}";
        $this->get($uri, $this->participant->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_show_clientNotActiveParticipant_error401()
    {
        $uri = $this->consultationSetupUri . "/{$this->consultationSetup->id}";
        $this->get($uri, $this->inactiveParticipant->client->token)
                ->seeStatusCode(401);
    }
    
    public function test_showAll()
    {
        $response = [
            'total' => 2, 
            'list' => [
                [
                    "id" => $this->consultationSetup->id,
                    "name" => $this->consultationSetup->name,
                    "sessionDuration" => $this->consultationSetup->sessionDuration,
                ],
                [
                    "id" => $this->consultationSetupOne->id,
                    "name" => $this->consultationSetupOne->name,
                    "sessionDuration" => $this->consultationSetupOne->sessionDuration,
                ],
            ],
        ];
        $this->get($this->consultationSetupUri, $this->participant->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_showAll_clientNotActiveParticipant_error401()
    {
        $this->get($this->consultationSetupUri, $this->inactiveParticipant->client->token)
                ->seeStatusCode(401);
    }
}
