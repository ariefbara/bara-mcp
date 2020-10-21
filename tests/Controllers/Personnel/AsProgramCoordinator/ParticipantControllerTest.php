<?php

namespace Tests\Controllers\Personnel\AsProgramCoordinator;

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
        
        $program = $this->coordinator->program;
        
        $user = new RecordOfUser(1);
        $this->connection->table('User')->insert($user->toArrayForDbEntry());
        
        $this->participantOne = new RecordOfParticipant($program, 1);
        $this->participantOne->active = false;
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
            "team" => null,
        ];
        
        $uri = $this->participantUri . "/{$this->participant->id}";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_show_personnelNotProgramCoordinator_403()
    {
        $uri = $this->participantUri . "/{$this->participant->id}";
        $this->get($uri, $this->removedCoordinator->personnel->token)
                ->seeStatusCode(403);
        
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
                    "team" => null,
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
                    "team" => null,
                ],
            ],
        ];
        $this->get($this->participantUri, $this->coordinator->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_showAll_activeStatusFilterSet()
    {
        $response = [
            "total" => 1, 
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
                    "team" => null,
                ],
            ],
        ];
        
        $uri = $this->participantUri . "?activeStatus=true";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_personnelNotCoordinator_403()
    {
        $this->get($this->participantUri, $this->removedCoordinator->personnel->token)
                ->seeStatusCode(403);
    }
}
