<?php

namespace Tests\Controllers\Personnel\ProgramConsultation;

use Tests\Controllers\ {
    Personnel\PersonnelTestCase,
    RecordPreparation\Firm\Program\RecordOfParticipant
};

class ParticipantTestCase extends ProgramConsultationTestCase
{
    protected $participantUri;
    /**
     *
     * @var RecordOfParticipant
     */
    protected $participant;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->participantUri = $this->programConsultationUri . "/participants";
        
        $this->connection->table("Participant")->truncate();
        
        $program = $this->programConsultation->program;
        
        $this->participant = new RecordOfParticipant($program, "main");
        $this->connection->table("Participant")->insert($this->participant->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Participant")->truncate();
    }
}
