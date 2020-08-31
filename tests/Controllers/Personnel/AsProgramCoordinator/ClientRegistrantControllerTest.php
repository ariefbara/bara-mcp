<?php

namespace Tests\Controllers\Personnel\AsProgramCoordinator;

use Tests\Controllers\RecordPreparation\Firm\{
    Program\RecordOfClientParticipant,
    Program\RecordOfClientRegistrant,
    Program\RecordOfParticipant,
    Program\RecordOfRegistrant,
    RecordOfClient
};

class ClientRegistrantControllerTest extends AsProgramCoordinatorTestCase
{

    protected $clientRegistrantUri;
    protected $clientRegistrant;
    protected $concludedClientRegistrant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientRegistrantUri = $this->asProgramCoordinatorUri . "/client-registrants";

        $this->connection->table('Client')->truncate();
        $this->connection->table('Registrant')->truncate();
        $this->connection->table('ClientRegistrant')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();

        $client = new RecordOfClient($this->coordinator->program->firm, 0);
        $client->email = 'purnama.adi@gmail.com';
        $this->connection->table('Client')->insert($client->toArrayForDbEntry());

        $registrant = new RecordOfRegistrant(0);
        $concludedRegistrant = new RecordOfRegistrant(1);
        $concludedRegistrant->concluded = true;
        $concludedRegistrant->note = 'cancelled';
        $this->connection->table('Registrant')->insert($registrant->toArrayForDbEntry());
        $this->connection->table('Registrant')->insert($concludedRegistrant->toArrayForDbEntry());

        $this->clientRegistrant = new RecordOfClientRegistrant($this->coordinator->program, $client, $registrant);
        $this->concludedClientRegistrant = new RecordOfClientRegistrant($this->coordinator->program, $client,
                $concludedRegistrant);
        $this->connection->table('ClientRegistrant')->insert($this->clientRegistrant->toArrayForDbEntry());
        $this->connection->table('ClientRegistrant')->insert($this->concludedClientRegistrant->toArrayForDbEntry());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Registrant')->truncate();
        $this->connection->table('ClientRegistrant')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
    }

    public function test_accept_200()
    {
        $response = [
            'id' => $this->clientRegistrant->id,
            "client" => [
                'id' => $this->clientRegistrant->client->id,
                'name' => $this->clientRegistrant->client->getFullName(),
            ],
            'registeredTime' => $this->clientRegistrant->registrant->registeredTime,
            "concluded" => true,
            "note" => 'accepted',
        ];
        
        $participantEntry = [
            'enrolledTime' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            'active' => true,
            'note' => null,
        ];

        $uri = $this->clientRegistrantUri . "/{$this->clientRegistrant->id}/accept";
        $this->patch($uri, [], $this->coordinator->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);

        $registrantEntry = [
            'id' => $this->clientRegistrant->registrant->id,
            "concluded" => true,
            "note" => 'accepted',
        ];
        $this->seeInDatabase('Registrant', $registrantEntry);

        $this->seeInDatabase('Participant', $participantEntry);

        $clientParticipant = [
            'Program_id' => $this->clientRegistrant->program->id,
            'Client_id' => $this->clientRegistrant->client->id,
        ];
        $this->seeInDatabase('ClientParticipant', $clientParticipant);
        
//see client email to check notification sent
    }

    public function test_accept_registrationAlreadyConcluded_403()
    {
        $uri = $this->clientRegistrantUri . "/{$this->concludedClientRegistrant->id}/accept";
        $this->patch($uri, [], $this->coordinator->personnel->token)
                ->seeStatusCode(403);
    }

    public function test_accept_alreadyParticipate_403()
    {
        $participant = new RecordOfParticipant(0);
        $this->connection->table('Participant')->insert($participant->toArrayForDbEntry());

        $clientParticipant = new RecordOfClientParticipant(
                $this->clientRegistrant->program, $this->clientRegistrant->client, $participant);
        $this->connection->table('ClientParticipant')->insert($clientParticipant->toArrayForDbEntry());

        $uri = $this->clientRegistrantUri . "/{$this->clientRegistrant->id}/accept";
        $this->patch($uri, [], $this->coordinator->personnel->token)
                ->seeStatusCode(403);
    }

    public function test_accept_conflictedParticipationAlreadyInactive_reenrollParticipation()
    {
        $participant = new RecordOfParticipant(0);
        $participant->active = false;
        $participant->note = 'quit';
        $this->connection->table('Participant')->insert($participant->toArrayForDbEntry());

        $clientParticipant = new RecordOfClientParticipant(
                $this->clientRegistrant->program, $this->clientRegistrant->client, $participant);
        $this->connection->table('ClientParticipant')->insert($clientParticipant->toArrayForDbEntry());

        $uri = $this->clientRegistrantUri . "/{$this->clientRegistrant->id}/accept";
        $this->patch($uri, [], $this->coordinator->personnel->token)
                ->seeStatusCode(200);

        $registrantEntry = [
            'id' => $this->clientRegistrant->registrant->id,
            "concluded" => true,
            "note" => 'accepted',
        ];
        $this->seeInDatabase('Registrant', $registrantEntry);

        $participantEntry = [
            'id' => $participant->id,
            "active" => true,
            "note" => null,
        ];
        $this->seeInDatabase('Participant', $participantEntry);
//see client email to check notification sent
    }

    public function test_accept_nonActiveCoordinator_401()
    {
        $uri = $this->clientRegistrantUri . "/{$this->clientRegistrant->id}/accept";
        $this->patch($uri, [], $this->removedCoordinator->personnel->token)
                ->seeStatusCode(401);
    }

    public function test_reject_200()
    {
        $response = [
            'id' => $this->clientRegistrant->id,
            "client" => [
                'id' => $this->clientRegistrant->client->id,
                'name' => $this->clientRegistrant->client->getFullName(),
            ],
            'registeredTime' => $this->clientRegistrant->registrant->registeredTime,
            "concluded" => true,
            "note" => 'rejected',
        ];

        $uri = $this->clientRegistrantUri . "/{$this->clientRegistrant->id}/reject";
        $this->patch($uri, [], $this->coordinator->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);

        $registrantEntry = [
            'id' => $this->clientRegistrant->registrant->id,
            "concluded" => true,
            "note" => 'rejected',
        ];
        $this->seeInDatabase('Registrant', $registrantEntry);
    }

    public function test_reject_alreadyConcluded_403()
    {
        $uri = $this->clientRegistrantUri . "/{$this->concludedClientRegistrant->id}/reject";
        $this->patch($uri, [], $this->coordinator->personnel->token)
                ->seeStatusCode(403);
    }

    public function test_reject_userIsNotActiveCoordinator_401()
    {
        $uri = $this->clientRegistrantUri . "/{$this->clientRegistrant->id}/reject";
        $this->patch($uri, [], $this->removedCoordinator->personnel->token)
                ->seeStatusCode(401);
    }

    public function test_show_200()
    {
        $response = [
            'id' => $this->clientRegistrant->id,
            "client" => [
                'id' => $this->clientRegistrant->client->id,
                'name' => $this->clientRegistrant->client->getFullName(),
            ],
            'registeredTime' => $this->clientRegistrant->registrant->registeredTime,
            "concluded" => $this->clientRegistrant->registrant->concluded,
            "note" => $this->clientRegistrant->registrant->note,
        ];

        $uri = $this->clientRegistrantUri . "/{$this->clientRegistrant->id}";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }

    public function test_show_userNotActiveCoordinator_401()
    {
        $uri = $this->clientRegistrantUri . "/{$this->clientRegistrant->id}";
        $this->get($uri, $this->removedCoordinator->personnel->token)
                ->seeStatusCode(401);
    }

    public function test_showAll_200()
    {
        $response = [
            'total' => 2,
            'list' => [
                [
                    'id' => $this->clientRegistrant->id,
                    "client" => [
                        'id' => $this->clientRegistrant->client->id,
                        'name' => $this->clientRegistrant->client->getFullName(),
                    ],
                    'registeredTime' => $this->clientRegistrant->registrant->registeredTime,
                    "concluded" => $this->clientRegistrant->registrant->concluded,
                    "note" => $this->clientRegistrant->registrant->note,
                ],
                [
                    'id' => $this->concludedClientRegistrant->id,
                    "client" => [
                        'id' => $this->concludedClientRegistrant->client->id,
                        'name' => $this->concludedClientRegistrant->client->getFullName(),
                    ],
                    'registeredTime' => $this->concludedClientRegistrant->registrant->registeredTime,
                    "concluded" => $this->concludedClientRegistrant->registrant->concluded,
                    "note" => $this->concludedClientRegistrant->registrant->note,
                ],
            ],
        ];
        $this->get($this->clientRegistrantUri, $this->coordinator->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }

    public function test_showAll_userNotActiveCoordinator_401()
    {
        $this->get($this->clientRegistrantUri, $this->removedCoordinator->personnel->token)
                ->seeStatusCode(401);
    }

}
