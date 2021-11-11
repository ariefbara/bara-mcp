<?php

namespace Tests\Controllers\Personnel;

use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;

class MentorTestCase extends PersonnelTestCase
{

    /**
     * 
     * @var RecordOfConsultant
     */
    protected $mentorOne;

    /**
     * 
     * @var RecordOfConsultant
     */
    protected $mentorTwo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Consultant')->truncate();

        $firm = $this->personnel->firm;

        $programOne = new RecordOfProgram($firm, '1');
        $programTwo = new RecordOfProgram($firm, '2');

        $this->mentorOne = new RecordOfConsultant($programOne, $this->personnel, '1');
        $this->mentorTwo = new RecordOfConsultant($programTwo, $this->personnel, '2');
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Consultant')->truncate();
    }

}
