<?php

namespace Tests\Controllers\Personnel\AsProgramConsultant;

use Tests\Controllers\RecordPreparation\{
    Firm\Program\RecordOfParticipant,
    RecordOfUser,
    User\RecordOfUserParticipant
};

class ParticipantTestCase extends AsProgramConsultantTestCase
{

    protected $participantUri;

    /**
     *
     * @var RecordOfParticipant
     */
    protected $participant;

    /**
     *
     * @var RecordOfUserParticipant
     */
    protected $userParticipant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participantUri = $this->asProgramConsultantUri . "/participants";
        $this->connection->table('User')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('UserParticipant')->truncate();

        $user = new RecordOfUser(0);
        $this->connection->table('User')->insert($user->toArrayForDbEntry());

        $this->participant = new RecordOfParticipant($this->consultant->program, 0);
        $this->connection->table('Participant')->insert($this->participant->toArrayForDbEntry());
        
        $this->userParticipant = new RecordOfUserParticipant($user, $this->participant);
        $this->connection->table('UserParticipant')->insert($this->userParticipant->toArrayForDbEntry());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('User')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
    }

}
