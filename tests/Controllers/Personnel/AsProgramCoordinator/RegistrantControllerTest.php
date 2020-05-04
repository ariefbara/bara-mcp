<?php

namespace Tests\Controllers\Personnel\AsProgramCoordinator;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\RecordOfParticipant,
    Firm\Program\RecordOfRegistrant,
    RecordOfClient
};

class RegistrantControllerTest extends AsProgramCoordinatorTestCase
{
    protected $registrantUri;
    protected $registrant;
    protected $registrantOne;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->registrantUri = $this->asProgramCoordinatorUri . "/registrants";
        $this->connection->table('Client')->truncate();
        $this->connection->table('Registrant')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientNotification')->truncate();
        
        $client = new RecordOfClient(0, 'client@email.org', 'password123');
        $clientOne = new RecordOfClient(1, 'clientOne@email.org', 'password123');
        $this->connection->table("Client")->insert($client->toArrayForDbEntry());
        $this->connection->table("Client")->insert($clientOne->toArrayForDbEntry());
        
        $this->registrant = new RecordOfRegistrant($this->coordinator->program, $client, 0);
        $this->registrantOne = new RecordOfRegistrant($this->coordinator->program, $clientOne, 1);
        $this->connection->table("Registrant")->insert($this->registrant->toArrayForDbEntry());
        $this->connection->table("Registrant")->insert($this->registrantOne->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Registrant')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientNotification')->truncate();
    }
    
    public function test_accept()
    {
        $uri = $this->registrantUri . "/{$this->registrant->id}/accept";
        $this->patch($uri, [], $this->coordinator->personnel->token)
                ->seeStatusCode(200);
        
        $registrantEntry = [
            "id" => $this->registrant->id,
            "concluded" => true,
            "note" => "accepted",
        ];
        $this->seeInDatabase('Registrant', $registrantEntry);
        
        $clientNotificationEntry = [
            "Client_id" => $this->registrant->client->id,
            "message" => "You have been accepted as participant of program {$this->coordinator->program->name}",
            "isRead" => false,
        ];
        $this->seeInDatabase("ClientNotification", $clientNotificationEntry);
    }
    public function test_accept_usetNotProgramCoordinator_error401()
    {
        $uri = $this->registrantUri . "/{$this->registrant->id}/accept";
        $this->patch($uri, [], $this->removedCoordinator->personnel->token)
                ->seeStatusCode(401);
    }
    public function test_accept_clientAlreadyActiveParticipant_error403()
    {
        $participant = new RecordOfParticipant($this->registrant->program, $this->registrant->client, 0);
        $this->connection->table('Participant')->insert($participant->toArrayForDbEntry());
        
        $uri = $this->registrantUri . "/{$this->registrant->id}/accept";
        $this->patch($uri, [], $this->coordinator->personnel->token)
                ->seeStatusCode(403);
    }
    public function test_accept_clientParticipationInProgramAlreadyInactive_acceptNormally()
    {
        $participant = new RecordOfParticipant($this->registrant->program, $this->registrant->client, 0);
        $participant->active = false;
        $this->connection->table('Participant')->insert($participant->toArrayForDbEntry());
        
        $uri = $this->registrantUri . "/{$this->registrant->id}/accept";
        $this->patch($uri, [], $this->coordinator->personnel->token)
                ->seeStatusCode(200);
        
    }
    
    public function test_reject()
    {
        $uri = $this->registrantUri . "/{$this->registrant->id}/reject";
        $this->patch($uri, [], $this->coordinator->personnel->token)
                ->seeStatusCode(200);
        $registrantEntry = [
            "id" => $this->registrant->id,
            "concluded" => true,
            "note" => "rejected",
        ];
        $this->seeInDatabase('Registrant', $registrantEntry);
    }
    public function test_reject_userNotCoordinator_error401()
    {
        $uri = $this->registrantUri . "/{$this->registrant->id}/reject";
        $this->patch($uri, [], $this->removedCoordinator->personnel->token)
                ->seeStatusCode(401);
    }
    
    public function test_show()
    {
        $response = [
            "id" => $this->registrant->id,
            "appliedTime" => $this->registrant->appliedTime,
            "concluded" => $this->registrant->concluded,
            "note" => $this->registrant->note,
            "client" => [
                "id" => $this->registrant->client->id,
                "name" => $this->registrant->client->name,
            ],
        ];
        $uri = $this->registrantUri . "/{$this->registrant->id}";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_show_userNotCoordinator_error401()
    {
        $uri = $this->registrantUri . "/{$this->registrant->id}";
        $this->get($uri, $this->removedCoordinator->personnel->token)
                ->seeStatusCode(401);
    }
    
    public function test_showAll()
    {
        $response = [
            "total" => 2, 
            "list" => [
                [
                    "id" => $this->registrant->id,
                    "appliedTime" => $this->registrant->appliedTime,
                    "concluded" => $this->registrant->concluded,
                    "note" => $this->registrant->note,
                    "client" => [
                        "id" => $this->registrant->client->id,
                        "name" => $this->registrant->client->name,
                    ],
                ],
                [
                    "id" => $this->registrantOne->id,
                    "appliedTime" => $this->registrantOne->appliedTime,
                    "concluded" => $this->registrantOne->concluded,
                    "note" => $this->registrantOne->note,
                    "client" => [
                        "id" => $this->registrantOne->client->id,
                        "name" => $this->registrantOne->client->name,
                    ],
                ],
            ],
        ];
        $this->get($this->registrantUri, $this->coordinator->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_showAll_userNotCoordinator_error401()
    {
        $this->get($this->registrantUri, $this->removedCoordinator->personnel->token)
                ->seeStatusCode(401);
    }
}
