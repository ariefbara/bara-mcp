<?php

namespace Tests\Controllers\Client;

use DateTimeImmutable;
use Query\Domain\Model\Firm\ParticipantTypes;
use Tests\Controllers\RecordPreparation\Firm\ {
    Client\RecordOfClientParticipant,
    Client\RecordOfClientRegistrant,
    Program\RecordOfParticipant,
    Program\RecordOfRegistrant,
    Program\RecordOfRegistrationPhase,
    RecordOfProgram
};

class ProgramRegistrationControllerTest extends ClientTestCase
{
    protected $programRegistrationUri;
    protected $programRegistration, $concludedProgramRegistration;
    protected $programParticipation;


    protected $program, $registrationPhase;
    
    protected $registerInput = [];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->programRegistrationUri = $this->clientUri . "/program-registrations";
        $this->connection->table('Program')->truncate();
        $this->connection->table('RegistrationPhase')->truncate();
        $this->connection->table('Registrant')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientRegistrant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        
        $this->program = new RecordOfProgram($this->client->firm, 0);
        $programOne = new RecordOfProgram($this->client->firm, 1);
        $programTwo = new RecordOfProgram($this->client->firm, 2);
        $this->connection->table('Program')->insert($this->program->toArrayForDbEntry());
        $this->connection->table('Program')->insert($programOne->toArrayForDbEntry());
        $this->connection->table('Program')->insert($programTwo->toArrayForDbEntry());
        
        $this->registrationPhase = new RecordOfRegistrationPhase($this->program, 0);
        $this->connection->table('RegistrationPhase')->insert($this->registrationPhase->toArrayForDbEntry());
        
        $registrant = new RecordOfRegistrant($programOne, 0);
        $concludedRegistrant = new RecordOfRegistrant($programTwo, 1);
        $concludedRegistrant->concluded = true;
        $concludedRegistrant->note = 'accepted';
        $this->connection->table('Registrant')->insert($registrant->toArrayForDbEntry());
        $this->connection->table('Registrant')->insert($concludedRegistrant->toArrayForDbEntry());
        
        $this->programRegistration = new RecordOfClientRegistrant($this->client, $registrant);
        $this->concludedProgramRegistration = new RecordOfClientRegistrant($this->client, $concludedRegistrant);
        $this->connection->table('ClientRegistrant')->insert($this->programRegistration->toArrayForDbEntry());
        $this->connection->table('ClientRegistrant')->insert($this->concludedProgramRegistration->toArrayForDbEntry());
        
        $this->registrationInput = [
            "programId" => $this->program->id,
        ];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Program')->truncate();
        $this->connection->table('RegistrationPhase')->truncate();
        $this->connection->table('Registrant')->truncate();
        $this->connection->table('ClientRegistrant')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
    }
    
    public function test_register_201()
    {
        $response = [
            "program" => [
                "id" => $this->program->id,
                "name" => $this->program->name,
            ],
            "registeredTime" => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            "concluded" => false,
            "note" => null,
        ];
        
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->client->token)
            ->seeStatusCode(201)
            ->seeJsonContains($response);
        
        $registrantEntry = [
            'Program_id' => $this->program->id,
            "registeredTime" => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            "concluded" => false,
            "note" => null,
        ];
        $this->seeInDatabase('Registrant', $registrantEntry);
        $clientRegistrantEntry = [
            'Client_id' => $this->client->id,
        ];
        $this->seeInDatabase('ClientRegistrant', $clientRegistrantEntry);
    }
    public function test_register_noOpenRegistrationPhaseAvailable_403()
    {
        $this->connection->table('RegistrationPhase')->truncate();
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->client->token)
            ->seeStatusCode(403);
    }
    public function test_register_noClientTypeInProgramParticipantTypesList_403()
    {
        $this->connection->table('Program')->truncate();
        $this->program->participantTypes = ParticipantTypes::USER_TYPE;
        $this->connection->table('Program')->insert($this->program->toArrayForDbEntry());
        
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->client->token)
            ->seeStatusCode(403);
    }
    
    public function test_register_removedProgram_403()
    {
        $this->connection->table('Program')->truncate();
        $this->program->removed = true;
        $this->connection->table('Program')->insert($this->program->toArrayForDbEntry());
        
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->client->token)
            ->seeStatusCode(403);
    }
    public function test_register_alreadyRegistedInProgram_403()
    {
        $registrant = new RecordOfRegistrant($this->program, 3);
        $clientRegistrant = new RecordOfClientRegistrant($this->client, $registrant);
        $this->connection->table('Registrant')->insert($registrant->toArrayForDbEntry());
        $this->connection->table('ClientRegistrant')->insert($clientRegistrant->toArrayForDbEntry());
        
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->client->token)
            ->seeStatusCode(403);
    }
    public function test_register_existingRegistrationAlreadyConcluded_201()
    {
        $registrant = new RecordOfRegistrant($this->program, 3);
        $registrant->concluded = true;
        $clientRegistrant = new RecordOfClientRegistrant($this->client, $registrant);
        $this->connection->table('Registrant')->insert($registrant->toArrayForDbEntry());
        $this->connection->table('ClientRegistrant')->insert($clientRegistrant->toArrayForDbEntry());
        
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->client->token)
            ->seeStatusCode(201);
    }
    public function test_register_inactiveClient_403()
    {
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->inactiveClient->token)
            ->seeStatusCode(403);
    }
    
    public function test_register_alreadyParticipateInProgram_403()
    {
        $participant = new RecordOfParticipant($this->program, 0);
        $clientParticipant = new RecordOfClientParticipant($this->client, $participant);
        $this->connection->table('Participant')->insert($participant->toArrayForDbEntry());
        $this->connection->table('ClientParticipant')->insert($clientParticipant->toArrayForDbEntry());
        
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->client->token)
            ->seeStatusCode(403);
    }
    public function test_register_conflictParticipantAlreadyInactive_201()
    {
        $participant = new RecordOfParticipant($this->program, 0);
        $participant->active = false;
        $clientParticipant = new RecordOfClientParticipant($this->client, $participant);
        $this->connection->table('Participant')->insert($participant->toArrayForDbEntry());
        $this->connection->table('ClientParticipant')->insert($clientParticipant->toArrayForDbEntry());
        
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->client->token)
            ->seeStatusCode(201);
    }
    
    public function test_cancel_200()
    {
        $uri = $this->programRegistrationUri . "/{$this->programRegistration->id}/cancel";
        $this->patch($uri, [], $this->client->token)
            ->seeStatusCode(200);
        $registrantEntry = [
            "id" => $this->programRegistration->registrant->id,
            "concluded" => true,
            "note" => 'cancelled',
        ];
        $this->seeInDatabase('Registrant', $registrantEntry);
    }
    public function test_cancel_alreadyConcluded_403()
    {
        $uri = $this->programRegistrationUri . "/{$this->concludedProgramRegistration->id}/cancel";
        $this->patch($uri, [], $this->client->token)
            ->seeStatusCode(403);
    }
    
    public function test_show()
    {
        $response = [
            "id" => $this->programRegistration->id,
            "program" => [
                "id" => $this->programRegistration->registrant->program->id,
                "name" => $this->programRegistration->registrant->program->name,
            ],
            "registeredTime" => $this->programRegistration->registrant->registeredTime,
            "concluded" => $this->programRegistration->registrant->concluded,
            "note" => $this->programRegistration->registrant->note,
        ];
        
        $uri = $this->programRegistrationUri . "/{$this->programRegistration->id}";
        $this->get($uri, $this->client->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
    
    public function test_showAll()
    {
        $response = [
            "total" => 2, 
            "list" => [
                [
                    "id" => $this->programRegistration->id,
                    "program" => [
                        "id" => $this->programRegistration->registrant->program->id,
                        "name" => $this->programRegistration->registrant->program->name,
                    ],
                    "registeredTime" => $this->programRegistration->registrant->registeredTime,
                    "concluded" => $this->programRegistration->registrant->concluded,
                    "note" => $this->programRegistration->registrant->note,
                ],
                [
                    "id" => $this->concludedProgramRegistration->id,
                    "program" => [
                        "id" => $this->concludedProgramRegistration->registrant->program->id,
                        "name" => $this->concludedProgramRegistration->registrant->program->name,
                    ],
                    "registeredTime" => $this->concludedProgramRegistration->registrant->registeredTime,
                    "concluded" => $this->concludedProgramRegistration->registrant->concluded,
                    "note" => $this->concludedProgramRegistration->registrant->note,
                ],
            ],
        ];
        
        $this->get($this->programRegistrationUri, $this->client->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
}
