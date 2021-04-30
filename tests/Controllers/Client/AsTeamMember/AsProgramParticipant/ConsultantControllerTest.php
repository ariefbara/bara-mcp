<?php

namespace Tests\Controllers\Client\AsTeamMember\AsProgramParticipant;

use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfDedicatedMentor;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;

class ConsultantControllerTest extends AsProgramParticipantTestCase
{
    protected $consultantUri;
    protected $consultant;
    protected $consultantOne;
    protected $consultantTwo_otherProgram;
    protected $dedicatedMentor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultantUri = $this->asProgramParticipantUri . "/consultants";
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('DedicatedMentor')->truncate();
        
        $participant = $this->programParticipant->participant;
        $program = $participant->program;
        $firm = $program->firm;
        $otherProgram = new RecordOfProgram($firm, 'other');
        $this->connection->table('Program')->insert($otherProgram->toArrayForDbEntry());
        
        $personnel = new RecordOfPersonnel($firm, 0, 'personnel@email.org', 'password123');
        $personnelOne = new RecordOfPersonnel($firm, 1, 'personnelOne@email.org', 'password123');
        $personnelTwo = new RecordOfPersonnel($firm, 2, 'personnelTwo@email.org', 'password123');
        $this->connection->table('Personnel')->insert($personnel->toArrayForDbEntry());
        $this->connection->table('Personnel')->insert($personnelOne->toArrayForDbEntry());
        $this->connection->table('Personnel')->insert($personnelTwo->toArrayForDbEntry());
        
        $this->consultant = new RecordOfConsultant($program, $personnel, 0);
        $this->consultantOne = new RecordOfConsultant($program, $personnelOne, 1);
        $this->consultantTwo_otherProgram = new RecordOfConsultant($otherProgram, $personnelTwo, 2);
        $this->connection->table('Consultant')->insert($this->consultant->toArrayForDbEntry());
        $this->connection->table('Consultant')->insert($this->consultantOne->toArrayForDbEntry());
        $this->connection->table('Consultant')->insert($this->consultantTwo_otherProgram->toArrayForDbEntry());
        
        $this->dedicatedMentor = new RecordOfDedicatedMentor($participant, $this->consultantOne, '1');
        $this->connection->table('DedicatedMentor')->insert($this->dedicatedMentor->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('DedicatedMentor')->truncate();
    }
    
    public function test_show()
    {
        $response = [
            "id" => $this->consultant->id,
            'personnel' => [
                "id" => $this->consultant->personnel->id,
                "name" => $this->consultant->personnel->getFullName(),
            ],
        ];
        $uri = $this->consultantUri . "/{$this->consultant->id}";
        $this->get($uri, $this->teamMember->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    
    public function test_showAll()
    {
        $response = [
            "total" => "2",
            "list" => [
                [
                    "id" => $this->consultant->id,
                    "personnelId" => $this->consultant->personnel->id,
                    "firstName" => $this->consultant->personnel->firstName,
                    "lastName" => $this->consultant->personnel->lastName,
                    'isDedicatedMentor' => "0",
                ],
                [
                    "id" => $this->consultantOne->id,
                    "personnelId" => $this->consultantOne->personnel->id,
                    "firstName" => $this->consultantOne->personnel->firstName,
                    "lastName" => $this->consultantOne->personnel->lastName,
                    'isDedicatedMentor' => "1",
                ],
            ],
        ];
        $this->get($this->consultantUri, $this->teamMember->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_showAll_orderByDedicatedMentorFirst()
    {
        $uri = $this->consultantUri . "?page=1&pageSize=1";
        $this->get($uri, $this->teamMember->client->token)
                ->seeStatusCode(200);
        $response = [
            'id' => $this->consultantOne->id,
        ];
        $this->seeJsonContains($response);
    }
}
