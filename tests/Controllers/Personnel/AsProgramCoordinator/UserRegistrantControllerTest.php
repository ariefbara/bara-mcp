<?php

namespace Tests\Controllers\Personnel\AsProgramCoordinator;

use DateTimeImmutable;
use Tests\Controllers\RecordPreparation\ {
    Firm\Program\RecordOfParticipant,
    Firm\Program\RecordOfRegistrant,
    Firm\Program\RecordOfUserParticipant,
    Firm\Program\RecordOfUserRegistrant,
    RecordOfUser
};

class UserRegistrantControllerTest extends AsProgramCoordinatorTestCase
{

    protected $userRegistrantUri;
    protected $userRegistrant;
    protected $concludedUserRegistrant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRegistrantUri = $this->asProgramCoordinatorUri . "/user-registrants";

        $this->connection->table('User')->truncate();
        $this->connection->table('Registrant')->truncate();
        $this->connection->table('UserRegistrant')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('UserParticipant')->truncate();

        $user = new RecordOfUser(0);
        $user->email = 'adi@barapraja.com';
        $this->connection->table('User')->insert($user->toArrayForDbEntry());

        $registrant = new RecordOfRegistrant(0);
        $concludedRegistrant = new RecordOfRegistrant(1);
        $concludedRegistrant->concluded = true;
        $concludedRegistrant->note = 'cancelled';
        $this->connection->table('Registrant')->insert($registrant->toArrayForDbEntry());
        $this->connection->table('Registrant')->insert($concludedRegistrant->toArrayForDbEntry());

        $this->userRegistrant = new RecordOfUserRegistrant($this->coordinator->program, $user, $registrant);
        $this->concludedUserRegistrant = new RecordOfUserRegistrant($this->coordinator->program, $user,
                $concludedRegistrant);
        $this->connection->table('UserRegistrant')->insert($this->userRegistrant->toArrayForDbEntry());
        $this->connection->table('UserRegistrant')->insert($this->concludedUserRegistrant->toArrayForDbEntry());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('User')->truncate();
        $this->connection->table('Registrant')->truncate();
        $this->connection->table('UserRegistrant')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
    }

    public function test_accept_200()
    {
        $response = [
            'id' => $this->userRegistrant->id,
            "user" => [
                'id' => $this->userRegistrant->user->id,
                'name' => $this->userRegistrant->user->getFullName(),
            ],
            'registeredTime' => $this->userRegistrant->registrant->registeredTime,
            "concluded" => true,
            "note" => 'accepted',
        ];

        $participantEntry = [
            'enrolledTime' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            'active' => true,
            'note' => null,
        ];
        
        $uri = $this->userRegistrantUri . "/{$this->userRegistrant->id}/accept";
        $this->patch($uri, [], $this->coordinator->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);

        $registrantEntry = [
            'id' => $this->userRegistrant->registrant->id,
            "concluded" => true,
            "note" => 'accepted',
        ];
        $this->seeInDatabase('Registrant', $registrantEntry);

        $this->seeInDatabase('Participant', $participantEntry);

        $userParticipant = [
            'Program_id' => $this->userRegistrant->program->id,
            'User_id' => $this->userRegistrant->user->id,
        ];
        $this->seeInDatabase('UserParticipant', $userParticipant);
//see user email to check notification sent
    }

    public function test_accept_registrationAlreadyConcluded_403()
    {
        $uri = $this->userRegistrantUri . "/{$this->concludedUserRegistrant->id}/accept";
        $this->patch($uri, [], $this->coordinator->personnel->token)
                ->seeStatusCode(403);
    }

    public function test_accept_alreadyParticipate_403()
    {
        $participant = new RecordOfParticipant(0);
        $this->connection->table('Participant')->insert($participant->toArrayForDbEntry());

        $userParticipant = new RecordOfUserParticipant(
                $this->userRegistrant->program, $this->userRegistrant->user, $participant);
        $this->connection->table('UserParticipant')->insert($userParticipant->toArrayForDbEntry());

        $uri = $this->userRegistrantUri . "/{$this->userRegistrant->id}/accept";
        $this->patch($uri, [], $this->coordinator->personnel->token)
                ->seeStatusCode(403);
    }

    public function test_accept_conflictedParticipationAlreadyInactive_reenrollParticipation()
    {
        $participant = new RecordOfParticipant(0);
        $participant->active = false;
        $participant->note = 'quit';
        $this->connection->table('Participant')->insert($participant->toArrayForDbEntry());

        $userParticipant = new RecordOfUserParticipant(
                $this->userRegistrant->program, $this->userRegistrant->user, $participant);
        $this->connection->table('UserParticipant')->insert($userParticipant->toArrayForDbEntry());

        $uri = $this->userRegistrantUri . "/{$this->userRegistrant->id}/accept";
        $this->patch($uri, [], $this->coordinator->personnel->token)
                ->seeStatusCode(200);

        $registrantEntry = [
            'id' => $this->userRegistrant->registrant->id,
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
//see user email to check notification sent
    }

    public function test_accept_nonActiveCoordinator_401()
    {
        $uri = $this->userRegistrantUri . "/{$this->userRegistrant->id}/accept";
        $this->patch($uri, [], $this->removedCoordinator->personnel->token)
                ->seeStatusCode(401);
    }

    public function test_reject_200()
    {
        $response = [
            'id' => $this->userRegistrant->id,
            "user" => [
                'id' => $this->userRegistrant->user->id,
                'name' => $this->userRegistrant->user->getFullName(),
            ],
            'registeredTime' => $this->userRegistrant->registrant->registeredTime,
            "concluded" => true,
            "note" => 'rejected',
        ];

        $uri = $this->userRegistrantUri . "/{$this->userRegistrant->id}/reject";
        $this->patch($uri, [], $this->coordinator->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);

        $registrantEntry = [
            'id' => $this->userRegistrant->registrant->id,
            "concluded" => true,
            "note" => 'rejected',
        ];
        $this->seeInDatabase('Registrant', $registrantEntry);
    }

    public function test_reject_alreadyConcluded_403()
    {
        $uri = $this->userRegistrantUri . "/{$this->concludedUserRegistrant->id}/reject";
        $this->patch($uri, [], $this->coordinator->personnel->token)
                ->seeStatusCode(403);
    }

    public function test_reject_userIsNotActiveCoordinator_401()
    {
        $uri = $this->userRegistrantUri . "/{$this->userRegistrant->id}/reject";
        $this->patch($uri, [], $this->removedCoordinator->personnel->token)
                ->seeStatusCode(401);
    }

    public function test_show_200()
    {
        $response = [
            'id' => $this->userRegistrant->id,
            "user" => [
                'id' => $this->userRegistrant->user->id,
                'name' => $this->userRegistrant->user->getFullName(),
            ],
            'registeredTime' => $this->userRegistrant->registrant->registeredTime,
            "concluded" => $this->userRegistrant->registrant->concluded,
            "note" => $this->userRegistrant->registrant->note,
        ];

        $uri = $this->userRegistrantUri . "/{$this->userRegistrant->id}";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }

    public function test_show_userNotActiveCoordinator_401()
    {
        $uri = $this->userRegistrantUri . "/{$this->userRegistrant->id}";
        $this->get($uri, $this->removedCoordinator->personnel->token)
                ->seeStatusCode(401);
    }

    public function test_showAll_200()
    {
        $response = [
            'total' => 2,
            'list' => [
                [
                    'id' => $this->userRegistrant->id,
                    "user" => [
                        'id' => $this->userRegistrant->user->id,
                        'name' => $this->userRegistrant->user->getFullName(),
                    ],
                    'registeredTime' => $this->userRegistrant->registrant->registeredTime,
                    "concluded" => $this->userRegistrant->registrant->concluded,
                    "note" => $this->userRegistrant->registrant->note,
                ],
                [
                    'id' => $this->concludedUserRegistrant->id,
                    "user" => [
                        'id' => $this->concludedUserRegistrant->user->id,
                        'name' => $this->concludedUserRegistrant->user->getFullName(),
                    ],
                    'registeredTime' => $this->concludedUserRegistrant->registrant->registeredTime,
                    "concluded" => $this->concludedUserRegistrant->registrant->concluded,
                    "note" => $this->concludedUserRegistrant->registrant->note,
                ],
            ],
        ];
        $this->get($this->userRegistrantUri, $this->coordinator->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }

    public function test_showAll_userNotActiveCoordinator_401()
    {
        $this->get($this->userRegistrantUri, $this->removedCoordinator->personnel->token)
                ->seeStatusCode(401);
    }

}
