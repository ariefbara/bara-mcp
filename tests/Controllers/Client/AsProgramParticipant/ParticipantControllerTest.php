<?php

namespace Tests\Controllers\Client\AsProgramParticipant;

use Tests\Controllers\RecordPreparation\ {
    Firm\Client\RecordOfClientParticipant,
    Firm\Program\RecordOfParticipant,
    Firm\RecordOfClient,
    Firm\RecordOfTeam,
    Firm\Team\RecordOfTeamProgramParticipation,
    RecordOfUser,
    User\RecordOfUserParticipant
};

class ParticipantControllerTest extends AsProgramParticipantTestCase
{
    protected $participantUri;
    protected $userParticipant;
    protected $clientParticipant;
    protected $teamParticipant;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->participantUri = $this->asProgramParticipantUri . "/participants";
        
        $this->connection->table("User")->truncate();
        $this->connection->table("Team")->truncate();
        $this->connection->table("UserParticipant")->truncate();
        $this->connection->table("TeamParticipant")->truncate();
        
        $program = $this->programParticipation->participant->program;
        $firm = $program->firm;
        
        $user = new RecordOfUser(0);
        $this->connection->table("User")->insert($user->toArrayForDbEntry());
        
        $client = new RecordOfClient($firm, 0);
        $clientOne = new RecordOfClient($firm, 1);
        $this->connection->table("Client")->insert($client->toArrayForDbEntry());
        $this->connection->table("Client")->insert($clientOne->toArrayForDbEntry());
        
        $team = new RecordOfTeam($firm, $clientOne, 0);
        $this->connection->table("Team")->insert($team->toArrayForDbEntry());
        
        $participant = new RecordOfParticipant($program, 0);
        $participantOne = new RecordOfParticipant($program, 1);
        $participantTwo = new RecordOfParticipant($program, 2);
        $this->connection->table("Participant")->insert($participant->toArrayForDbEntry());
        $this->connection->table("Participant")->insert($participantOne->toArrayForDbEntry());
        $this->connection->table("Participant")->insert($participantTwo->toArrayForDbEntry());
        
        $this->userParticipant = new RecordOfUserParticipant($user, $participant);
        $this->connection->table("UserParticipant")->insert($this->userParticipant->toArrayForDbEntry());
        
        $this->clientParticipant = new RecordOfClientParticipant($client, $participantOne);
        $this->connection->table("ClientParticipant")->insert($this->clientParticipant->toArrayForDbEntry());
        
        $this->teamParticipant = new RecordOfTeamProgramParticipation($team, $participantTwo);
        $this->connection->table("TeamParticipant")->insert($this->teamParticipant->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("User")->truncate();
        $this->connection->table("Team")->truncate();
        $this->connection->table("UserParticipant")->truncate();
        $this->connection->table("TeamParticipant")->truncate();
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->userParticipant->participant->id,
            "enrolledTime" => $this->userParticipant->participant->enrolledTime,
            "active" => $this->userParticipant->participant->active,
            "note" => $this->userParticipant->participant->note,
            "user" => [
                "id" => $this->userParticipant->user->id,
                "name" => $this->userParticipant->user->getFullName(),
            ],
            "client" => null,
            "team" => null,
        ];
        
        $uri = $this->participantUri . "/{$this->userParticipant->participant->id}";
        $this->get($uri, $this->programParticipation->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_show_inactiveParticipant_403()
    {
        $uri = $this->participantUri . "/{$this->userParticipant->participant->id}";
        $this->get($uri, $this->inactiveProgramParticipation->client->token)
                ->seeStatusCode(403);
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 5,
            "list" => [
                [
                    "id" => $this->userParticipant->participant->id,
                    "enrolledTime" => $this->userParticipant->participant->enrolledTime,
                    "active" => $this->userParticipant->participant->active,
                    "note" => $this->userParticipant->participant->note,
                    "user" => [
                        "id" => $this->userParticipant->user->id,
                        "name" => $this->userParticipant->user->getFullName(),
                    ],
                    "client" => null,
                    "team" => null,
                ],
                [
                    "id" => $this->programParticipation->participant->id,
                    "enrolledTime" => $this->programParticipation->participant->enrolledTime,
                    "active" => $this->programParticipation->participant->active,
                    "note" => $this->programParticipation->participant->note,
                    "client" => [
                        "id" => $this->programParticipation->client->id,
                        "name" => $this->programParticipation->client->getFullName(),
                    ],
                    "user" => null,
                    "team" => null,
                ],
                [
                    "id" => $this->inactiveProgramParticipation->participant->id,
                    "enrolledTime" => $this->inactiveProgramParticipation->participant->enrolledTime,
                    "active" => $this->inactiveProgramParticipation->participant->active,
                    "note" => $this->inactiveProgramParticipation->participant->note,
                    "client" => [
                        "id" => $this->inactiveProgramParticipation->client->id,
                        "name" => $this->inactiveProgramParticipation->client->getFullName(),
                    ],
                    "user" => null,
                    "team" => null,
                ],
                [
                    "id" => $this->clientParticipant->participant->id,
                    "enrolledTime" => $this->clientParticipant->participant->enrolledTime,
                    "active" => $this->clientParticipant->participant->active,
                    "note" => $this->clientParticipant->participant->note,
                    "client" => [
                        "id" => $this->clientParticipant->client->id,
                        "name" => $this->clientParticipant->client->getFullName(),
                    ],
                    "user" => null,
                    "team" => null,
                ],
                [
                    "id" => $this->teamParticipant->participant->id,
                    "enrolledTime" => $this->teamParticipant->participant->enrolledTime,
                    "active" => $this->teamParticipant->participant->active,
                    "note" => $this->teamParticipant->participant->note,
                    "team" => [
                        "id" => $this->teamParticipant->team->id,
                        "name" => $this->teamParticipant->team->name,
                    ],
                    "user" => null,
                    "client" => null,
                ],
            ],
        ];
        
        $this->get($this->participantUri, $this->programParticipation->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_inactiveParticipant_403()
    {
        $this->get($this->participantUri, $this->inactiveProgramParticipation->client->token)
                ->seeStatusCode(403);
    }
}
