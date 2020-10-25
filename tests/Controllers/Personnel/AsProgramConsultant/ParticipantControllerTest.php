<?php

namespace Tests\Controllers\Personnel\AsProgramConsultant;

use Tests\Controllers\RecordPreparation\ {
    Firm\Client\RecordOfClientParticipant,
    Firm\Program\RecordOfParticipant,
    Firm\RecordOfClient,
    Firm\RecordOfTeam,
    Firm\Team\RecordOfTeamProgramParticipation,
    RecordOfUser
};

class ParticipantControllerTest extends ParticipantTestCase
{
    protected $participantOne_client;
    protected $participantTwo_team;
    
    protected $clientParticipant;
    protected $teamParticipant;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->connection->table("Client")->truncate();
        $this->connection->table("Team")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("TeamParticipant")->truncate();
        
        $program = $this->consultant->program;
        $firm = $program->firm;
        
        $client = new RecordOfClient($firm, 0);
        $this->connection->table("Client")->insert($client->toArrayForDbEntry());
        
        $team = new RecordOfTeam($firm, $client, 0);
        $this->connection->table("Team")->insert($team->toArrayForDbEntry());
        
        $this->participantOne_client = new RecordOfParticipant($program, 1);
        $this->participantTwo_team = new RecordOfParticipant($program, 2);
        $this->connection->table('Participant')->insert($this->participantOne_client->toArrayForDbEntry());
        $this->connection->table('Participant')->insert($this->participantTwo_team->toArrayForDbEntry());
        
        
        $this->clientParticipant = new RecordOfClientParticipant($client, $this->participantOne_client);
        $this->connection->table("ClientParticipant")->insert($this->clientParticipant->toArrayForDbEntry());
        
        $this->teamParticipant = new RecordOfTeamProgramParticipation($team, $this->participantTwo_team);
        $this->connection->table("TeamParticipant")->insert($this->teamParticipant->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Client")->truncate();
        $this->connection->table("Team")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("TeamParticipant")->truncate();
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
            "total" => 3, 
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
                    "id" => $this->participantOne_client->id,
                    "enrolledTime" => $this->participantOne_client->enrolledTime,
                    "active" => $this->participantOne_client->active,
                    "note" => $this->participantOne_client->note,
                    "user" => null,
                    "client" => [
                        "id" => $this->clientParticipant->client->id,
                        "name" => $this->clientParticipant->client->getFullName(),
                    ],
                    "team" => null,
                ],
                [
                    "id" => $this->participantTwo_team->id,
                    "enrolledTime" => $this->participantTwo_team->enrolledTime,
                    "active" => $this->participantTwo_team->active,
                    "note" => $this->participantTwo_team->note,
                    "user" => null,
                    "client" => null,
                    "team" => [
                        "id" => $this->teamParticipant->team->id,
                        "name" => $this->teamParticipant->team->name,
                    ],
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
