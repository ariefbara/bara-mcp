<?php

namespace Tests\Controllers\Personnel\ProgramConsultation;

use Tests\Controllers\RecordPreparation\ {
    Firm\Client\RecordOfClientParticipant,
    Firm\Program\RecordOfParticipant,
    Firm\RecordOfClient,
    Firm\RecordOfTeam,
    Firm\Team\RecordOfTeamProgramParticipation,
    RecordOfUser,
    User\RecordOfUserParticipant
};

class ParticipantControllerTest extends ParticipantTestCase
{
    protected $participantOne;
    protected $participantTwo;
    
    protected $teamParticipant;
    protected $clientParticipant;
    protected $userParticipant;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table("Team")->truncate();
        $this->connection->table("Client")->truncate();
        $this->connection->table("User")->truncate();
        $this->connection->table("TeamParticipant")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("UserParticipant")->truncate();
        
        $program = $this->programConsultation->program;
        $firm = $program->firm;
        
        
        $this->participantOne = new RecordOfParticipant($program, 1);
        $this->participantTwo = new RecordOfParticipant($program, 2);
        $this->connection->table("Participant")->insert($this->participantOne->toArrayForDbEntry());
        $this->connection->table("Participant")->insert($this->participantTwo->toArrayForDbEntry());
        
        $client = new RecordOfClient($firm, 0);
        $this->connection->table("Client")->insert($client->toArrayForDbEntry());
        
        $team = new RecordOfTeam($firm, $client, 0);
        $this->connection->table("Team")->insert($team->toArrayForDbEntry());
        
        $this->teamParticipant = new RecordOfTeamProgramParticipation($team, $this->participant);
        $this->connection->table("TeamParticipant")->insert($this->teamParticipant->toArrayForDbEntry());
        
        $this->clientParticipant = new RecordOfClientParticipant($client, $this->participantOne);
        $this->connection->table("ClientParticipant")->insert($this->clientParticipant->toArrayForDbEntry());
        
        $user = new RecordOfUser(0);
        $this->connection->table("User")->insert($user->toArrayForDbEntry());
        
        $this->userParticipant = new RecordOfUserParticipant($user, $this->participantTwo);
        $this->connection->table("UserParticipant")->insert($this->userParticipant->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Team")->truncate();
        $this->connection->table("Client")->truncate();
        $this->connection->table("User")->truncate();
        $this->connection->table("TeamParticipant")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("UserParticipant")->truncate();
    }
    
    public function test_show_200()
    {
        $repsonse = [
            "id" => $this->participant->id,
            "enrolledTime" => $this->participant->enrolledTime,
            "note" => $this->participant->note,
            "active" => $this->participant->active,
            "team" => [
                "id" => $this->teamParticipant->team->id,
                "name" => $this->teamParticipant->team->name,
            ],
            "client" => null,
            "user" => null,
        ];
        
        $uri = $this->participantUri . "/{$this->participant->id}";
        $this->get($uri, $this->programConsultation->personnel->token)
                ->seeJsonContains($repsonse)
                ->seeStatusCode(200);
    }
    public function test_show_removedConsultant_403()
    {
        $this->removeProgramConsultation();
        $uri = $this->participantUri . "/{$this->participant->id}";
        $this->get($uri, $this->programConsultation->personnel->token)
                ->seeStatusCode(403);
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 3, 
            "list" => [
                [
                    "id" => $this->participant->id,
                    "enrolledTime" => $this->participant->enrolledTime,
                    "note" => $this->participant->note,
                    "active" => $this->participant->active,
                    "team" => [
                        "id" => $this->teamParticipant->team->id,
                        "name" => $this->teamParticipant->team->name,
                    ],
                    "client" => null,
                    "user" => null,
                ],
                [
                    "id" => $this->participantOne->id,
                    "enrolledTime" => $this->participantOne->enrolledTime,
                    "note" => $this->participantOne->note,
                    "active" => $this->participantOne->active,
                    "team" => null,
                    "client" => [
                        "id" => $this->clientParticipant->client->id,
                        "name" => $this->clientParticipant->client->getFullName(),
                    ],
                    "user" => null,
                ],
                [
                    "id" => $this->participantTwo->id,
                    "enrolledTime" => $this->participantTwo->enrolledTime,
                    "note" => $this->participantTwo->note,
                    "active" => $this->participantTwo->active,
                    "team" => null,
                    "client" => null,
                    "user" => [
                        "id" => $this->userParticipant->user->id,
                        "name" => $this->userParticipant->user->getFullName(),
                    ],
                ],
            ],
        ];
        $this->get($this->participantUri, $this->programConsultation->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_removeConsultant_403()
    {
        $this->removeProgramConsultation();
        $this->get($this->participantUri, $this->programConsultation->personnel->token)
                ->seeStatusCode(403);
        
    }
}
