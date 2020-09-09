<?php

namespace Tests\Controllers\Personnel\AsProgramConsultant;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\RecordOfParticipant,
    RecordOfUser,
    User\RecordOfUserParticipant
};

class ParticipantControllerTest extends ParticipantTestCase
{
    protected $participantOne;
    protected $userParticipantOne;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $user = new RecordOfUser(1);
        $this->connection->table('User')->insert($user->toArrayForDbEntry());
        
        $this->participantOne = new RecordOfParticipant($this->consultant->program, 1);
        $this->connection->table('Participant')->insert($this->participantOne->toArrayForDbEntry());
        
        $this->userParticipantOne = new RecordOfUserParticipant($user, $this->participantOne);
        $this->connection->table('UserParticipant')->insert($this->userParticipantOne->toArrayForDbEntry());
        
    }
    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    public function test_show()
    {
        $response = [
            "id" => $this->participant->id,
            "enrolledTime" => $this->participant->enrolledTime,
            "active" => $this->participant->active,
            "note" => $this->participant->note,
            "user" => [
                "id" => $this->userParticipant->user->id,
                "name" => $this->userParticipant->user->getFullName(),
            ],
            "client" => null,
        ];
        
        $uri = $this->participantUri . "/{$this->participant->id}";
        $this->get($uri, $this->consultant->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_show_personnelNotProgramConsultant_error401()
    {
        $uri = $this->participantUri . "/{$this->participant->id}";
        $this->get($uri, $this->removedConsultant->personnel->token)
                ->seeStatusCode(401);
        
    }
    
    public function test_showAll()
    {
        $response = [
            "total" => 2, 
            "list" => [
                [
                    "id" => $this->participant->id,
                    "enrolledTime" => $this->participant->enrolledTime,
                    "active" => $this->participant->active,
                    "note" => $this->participant->note,
                    "user" => [
                        "id" => $this->userParticipant->user->id,
                        "name" => $this->userParticipant->user->getFullName(),
                    ],
                    "client" => null,
                ],
                [
                    "id" => $this->participantOne->id,
                    "enrolledTime" => $this->participantOne->enrolledTime,
                    "active" => $this->participantOne->active,
                    "note" => $this->participantOne->note,
                    "user" => [
                        "id" => $this->userParticipantOne->user->id,
                        "name" => $this->userParticipantOne->user->getFullName(),
                    ],
                    "client" => null,
                ],
            ],
        ];
        $this->get($this->participantUri, $this->consultant->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_showAll_personnelNotProgramConsultant_error401()
    {
        $this->get($this->participantUri, $this->removedConsultant->personnel->token)
                ->seeStatusCode(401);
    }
}
