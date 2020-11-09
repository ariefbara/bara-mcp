<?php

namespace Tests\Controllers\Manager\Program;

use Tests\Controllers\ {
    Manager\ProgramTestCase,
    RecordPreparation\Firm\Client\RecordOfClientParticipant,
    RecordPreparation\Firm\Program\RecordOfParticipant,
    RecordPreparation\Firm\RecordOfClient,
    RecordPreparation\Firm\RecordOfTeam,
    RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation,
    RecordPreparation\RecordOfUser,
    RecordPreparation\User\RecordOfUserParticipant
};

class ParticipantControllerTest extends ProgramTestCase
{
    protected $participantUri;
    protected $userParticipant;
    protected $clientParticipant;
    protected $teamParticipant;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->participantUri = $this->programUri . "/{$this->program->id}/participants";
        
        $this->connection->table("User")->truncate();
        $this->connection->table("Client")->truncate();
        $this->connection->table("Team")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("UserParticipant")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("TeamParticipant")->truncate();
        
        $user = new RecordOfUser(0);
        $this->connection->table("User")->insert($user->toArrayForDbEntry());
        
        $client = new RecordOfClient($this->firm, 0);
        $clientOne = new RecordOfClient($this->firm, 1);
        $this->connection->table("Client")->insert($client->toArrayForDbEntry());
        $this->connection->table("Client")->insert($clientOne->toArrayForDbEntry());
        
        $team = new RecordOfTeam($this->firm, $clientOne, 0);
        $this->connection->table("Team")->insert($team->toArrayForDbEntry());
        
        $participant = new RecordOfParticipant($this->program, 0);
        $participantOne = new RecordOfParticipant($this->program, 1);
        $participantTwo = new RecordOfParticipant($this->program, 2);
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
        $this->connection->table("Client")->truncate();
        $this->connection->table("Team")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("UserParticipant")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
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
        $this->get($uri, $this->manager->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_show_inactiveManager_401()
    {
        $uri = $this->participantUri . "/{$this->userParticipant->participant->id}";
        $this->get($uri, $this->removedManager->token)
                ->seeStatusCode(401);
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 3,
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
        
        $this->get($this->participantUri, $this->manager->token)
                ->seeStatusCode(200);
    }
    public function test_showAll_inactiveManager_401()
    {
        $this->get($this->participantUri, $this->removedManager->token)
                ->seeStatusCode(401);
    }
}
