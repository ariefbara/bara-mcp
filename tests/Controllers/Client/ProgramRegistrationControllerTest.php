<?php

namespace Tests\Controllers\Client;

use DateTimeImmutable;
use Query\Domain\Model\Firm\ParticipantTypes;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientRegistrant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfProgramsProfileForm;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfRegistrant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfRegistrationPhase;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProfileForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;

class ProgramRegistrationControllerTest extends ClientTestCase
{
    protected $programRegistrationUri;
    protected $programRegistration, $concludedProgramRegistration;
    protected $programParticipation;


    protected $program, $registrationPhase;
    protected $programsProfileFormOne;

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
        $this->connection->table('Form')->truncate();
        $this->connection->table('ProfileForm')->truncate();
        $this->connection->table('ProgramsProfileForm')->truncate();
        
        $firm = $this->client->firm;
                
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
        
        $formOne = new RecordOfForm('1');
        
        $profileFormOne = new RecordOfProfileForm($firm, $formOne);
        
        $this->programsProfileFormOne = new RecordOfProgramsProfileForm($this->program, $profileFormOne, '1');
        
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
        $this->connection->table('Form')->truncate();
        $this->connection->table('ProfileForm')->truncate();
        $this->connection->table('ProgramsProfileForm')->truncate();
    }
    
    public function test_register_201()
    {
        $this->programsProfileFormOne->profileForm->insert($this->connection);
        $this->programsProfileFormOne->insert($this->connection);
        $response = [
            "program" => [
                "id" => $this->program->id,
                "name" => $this->program->name,
                "hasProfileForm" => true,
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
    
    public function test_register_removedProgram_404()
    {
        $this->connection->table('Program')->truncate();
        $this->program->removed = true;
        $this->connection->table('Program')->insert($this->program->toArrayForDbEntry());
        
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->client->token)
            ->seeStatusCode(404);
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
                "hasProfileForm" => false,
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
                        "hasProfileForm" => false,
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
                        "hasProfileForm" => false,
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
    public function test_showAll_filterConcludedStatus()
    {
        $uri = $this->programRegistrationUri . '?concludedStatus=false';
        $this->get($uri, $this->client->token)
                ->seeStatusCode(200);
        $response = [
            "total" => 1, 
            "list" => [
                [
                    "id" => $this->programRegistration->id,
                    "program" => [
                        "id" => $this->programRegistration->registrant->program->id,
                        "name" => $this->programRegistration->registrant->program->name,
                        "hasProfileForm" => false,
                    ],
                    "registeredTime" => $this->programRegistration->registrant->registeredTime,
                    "concluded" => $this->programRegistration->registrant->concluded,
                    "note" => $this->programRegistration->registrant->note,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
}
