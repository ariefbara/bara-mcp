<?php

namespace Tests\Controllers\Client;

use Tests\Controllers\RecordPreparation\Firm\ {
    Client\RecordOfClientParticipant,
    Program\RecordOfParticipant,
    RecordOfProgram
};

class ProgramParticipationTestCase extends ClientTestCase
{

    protected $programParticipationUri;

    /**
     *
     * @var RecordOfClientParticipant
     */
    protected $programParticipation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->programParticipationUri = $this->clientUri . "/program-participations";

        $this->connection->table('Program')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('Participant')->truncate();

        $program = new RecordOfProgram($this->client->firm, 999);
        $this->connection->table('Program')->insert($program->toArrayForDbEntry());

        $participant = new RecordOfParticipant($program, 999);
        $this->connection->table('Participant')->insert($participant->toArrayForDbEntry());

        $this->programParticipation = new RecordOfClientParticipant($this->client, $participant);
        $this->connection->table('ClientParticipant')->insert($this->programParticipation->toArrayForDbEntry());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Program')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('Participant')->truncate();
    }
    
    protected function setParticipantInactive()
    {
        $this->connection->table("Participant")->truncate();
        $this->programParticipation->participant->active = false;
        $this->connection->table("Participant")->insert($this->programParticipation->participant->toArrayForDbEntry());
    }

}
