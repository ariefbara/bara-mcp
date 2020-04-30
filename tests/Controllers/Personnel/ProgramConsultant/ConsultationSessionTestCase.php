<?php

namespace Tests\Controllers\Personnel\ProgramConsultant;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\Participant\RecordOfConsultationSession,
    Firm\Program\RecordOfConsultationSetup,
    Firm\Program\RecordOfParticipant,
    Firm\RecordOfConsultationFeedbackForm,
    RecordOfClient,
    Shared\RecordOfForm
};

class ConsultationSessionTestCase extends ProgramConsultantTestCase
{
    protected $consultationSessionUri;
    /**
     *
     * @var RecordOfConsultationSession
     */
    protected $consultationSession;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->consultationSessionUri = $this->programConsultantUri. "/consultation-sessions";
        $this->connection->table('Form')->truncate();
        $this->connection->table('ConsultationFeedbackForm')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('ConsultationSession')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Participant')->truncate();
        
        $form = new RecordOfForm(0);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());
        
        $consultationFeedbackForm = new RecordOfConsultationFeedbackForm($this->programConsultant->program->firm, $form);
        $this->connection->table("ConsultationFeedbackForm")->insert($consultationFeedbackForm->toArrayForDbEntry());
        
        $consultationSetup = new RecordOfConsultationSetup($this->programConsultant->program, $consultationFeedbackForm, $consultationFeedbackForm, 0);
        $this->connection->table("ConsultationSetup")->insert($consultationSetup->toArrayForDbEntry());
        
        $client = new RecordOfClient(0, 'client@email.org', 'password123');
        $this->connection->table("Client")->insert($client->toArrayForDbEntry());
        
        $participant = new RecordOfParticipant($this->programConsultant->program, $client, 0);
        $this->connection->table("Participant")->insert($participant->toArrayForDbEntry());
        
        $this->consultationSession = new RecordOfConsultationSession($consultationSetup, $participant, $this->programConsultant, 0);
        $this->connection->table("ConsultationSession")->insert($this->consultationSession->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Form')->truncate();
        $this->connection->table('ConsultationFeedbackForm')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('ConsultationSession')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Participant')->truncate();
    }
}
