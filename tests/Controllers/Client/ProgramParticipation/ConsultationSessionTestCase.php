<?php

namespace Tests\Controllers\Client\ProgramParticipation;

use Tests\Controllers\ {
    Client\ProgramParticipationTestCase,
    RecordPreparation\Firm\Program\Participant\RecordOfConsultationSession,
    RecordPreparation\Firm\Program\RecordOfConsultant,
    RecordPreparation\Firm\Program\RecordOfConsultationSetup,
    RecordPreparation\Firm\RecordOfFeedbackForm,
    RecordPreparation\Firm\RecordOfPersonnel,
    RecordPreparation\Shared\RecordOfForm
};

class ConsultationSessionTestCase extends ProgramParticipationTestCase
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
        
        $this->consultationSessionUri = $this->programParticipationUri . "/{$this->programParticipation->id}/consultation-sessions";
        $this->connection->table('Form')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ConsultationSession')->truncate();
        
        $program = $this->programParticipation->participant->program;
        $firm = $program->firm;
        
        $form = new RecordOfForm(0);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());
        
        $feedbackForm = new RecordOfFeedbackForm($firm, $form);
        $this->connection->table("FeedbackForm")->insert($feedbackForm->toArrayForDbEntry());
        
        $consultationSetup = new RecordOfConsultationSetup($program, $feedbackForm, $feedbackForm, 0);
        $this->connection->table("ConsultationSetup")->insert($consultationSetup->toArrayForDbEntry());
        
        $personnel = new RecordOfPersonnel($firm, 0, "personnel@email.org", 'password123');
        $this->connection->table("Personnel")->insert($personnel->toArrayForDbEntry());
        
        
        $consultant = new RecordOfConsultant($program, $personnel, 0);
        $this->connection->table("Consultant")->insert($consultant->toArrayForDbEntry());
        
        $this->consultationSession = new RecordOfConsultationSession($consultationSetup, $this->programParticipation->participant, $consultant, 0);
        $this->connection->table("ConsultationSession")->insert($this->consultationSession->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Form')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ConsultationSession')->truncate();
    }
}
