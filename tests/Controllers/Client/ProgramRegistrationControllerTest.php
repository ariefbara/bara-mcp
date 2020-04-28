<?php

namespace Tests\Controllers\Client;

use DateTime;
use Tests\Controllers\RecordPreparation\ {
    Firm\Program\RecordOfParticipant,
    Firm\Program\RecordOfRegistrant,
    Firm\Program\RecordOfRegistrationPhase,
    Firm\RecordOfProgram,
    RecordOfFirm
};

class ProgramRegistrationControllerTest extends ClientTestCase
{
    protected $programRegistrationUri;
    protected $programRegistrant, $programRegistrantOne;
    protected $firm, $program, $unpublishedProgram;
    protected $registrationPhase;
    protected $registrationInput;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->programRegistrationUri = $this->clientUri . "/program-registrations";
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('RegistrationPhase')->truncate();
        $this->connection->table('Registrant')->truncate();
        $this->connection->table('Participant')->truncate();
        
        $this->firm = new RecordOfFirm(0, 'firm_identifier');
        $this->connection->table('Firm')->insert($this->firm->toArrayForDbEntry());
        
        $this->program = new RecordOfProgram($this->firm, 2);
        $this->program->published = true;
        $this->unpublishedProgram = new RecordOfProgram($this->firm, 1);
        $this->connection->table('Program')->insert($this->program->toArrayForDbEntry());
        $this->connection->table('Program')->insert($this->unpublishedProgram->toArrayForDbEntry());
        
        $this->registrationPhase = new RecordOfRegistrationPhase($this->program, 0);
        $unpublishedProgram_registrationPhase = new RecordOfRegistrationPhase($this->unpublishedProgram, 1);
        $this->connection->table('RegistrationPhase')->insert($this->registrationPhase->toArrayForDbEntry());
        $this->connection->table('RegistrationPhase')->insert($unpublishedProgram_registrationPhase->toArrayForDbEntry());
        
        $this->programRegistrant = new RecordOfRegistrant($this->program, $this->client, 0);
        $this->programRegistrant->concluded = true;
        $this->programRegistrantOne = new RecordOfRegistrant($this->unpublishedProgram, $this->client, 1);
        $this->connection->table('Registrant')->insert($this->programRegistrant->toArrayForDbEntry());
        $this->connection->table('Registrant')->insert($this->programRegistrantOne->toArrayForDbEntry());
        
        
        $this->registrationInput = [
            "firmId" => $this->firm->id,
            "programId" => $this->program->id,
        ];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('RegistrationPhase')->truncate();
        $this->connection->table('Registrant')->truncate();
        $this->connection->table('Participant')->truncate();
    }
    
    public function test_apply()
    {
        $response = [
            "program" => [
                "id" => $this->program->id,
                "name" => $this->program->name,
                "firm" => [
                    "id" => $this->program->firm->id,
                    "name" => $this->program->firm->name,
                ],
            ],
            "concluded" => false,
            "note" => null,
        ];
        
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->client->token)
            ->seeStatusCode(201)
            ->seeJsonContains($response);
        
        $programRegistrantEntry = [
            "Program_id" => $this->program->id,
            "Client_id" => $this->client->id,
            "concluded" => false,
            "note" => null,
        ];
        $this->seeInDatabase('Registrant', $programRegistrantEntry);
    }
    public function test_apply_noOpenRegistrationPhaseAvailable_error403()
    {
        $this->connection->table('RegistrationPhase')->truncate();
        $registrationPhase = new RecordOfRegistrationPhase($this->program, 1);
        $registrationPhase->startDate = (new DateTime("+7 days"))->format('Y-m-d');
        $registrationPhase->endDate = (new DateTime("+30 days"))->format('Y-m-d');
        $this->connection->table('RegistrationPhase')->insert($registrationPhase->toArrayForDbEntry());
        
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->client->token)
            ->seeStatusCode(403);
    }
    public function test_apply_unpublishedProgram_error403()
    {
        $this->program->published = false;
        $this->connection->table('Program')->truncate();
        $this->connection->table('Program')->insert($this->program->toArrayForDbEntry());
        
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->client->token)
            ->seeStatusCode(403);
    }
    public function test_apply_alreadyRegistedInProgram_error403()
    {
        $conflictRegistrant = new RecordOfRegistrant($this->program, $this->client, 3);
        $this->connection->table('Registrant')->insert($conflictRegistrant->toArrayForDbEntry());
        
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->client->token)
            ->seeStatusCode(403);
    }
    public function test_apply_existingProgramRegistrationAlreadyConcluded_applyNormally()
    {
        $concludedRegistrant = new RecordOfRegistrant($this->program, $this->client, 3);
        $concludedRegistrant->concluded = true;
        $this->connection->table('Registrant')->insert($concludedRegistrant->toArrayForDbEntry());
        
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->client->token)
            ->seeStatusCode(201);
    }    
    public function test_apply_alreadyParticipateInProgram_error403()
    {
        $conflictParticipant = new RecordOfParticipant($this->program, $this->client, 0);
        $this->connection->table('Participant')->insert($conflictParticipant->toArrayForDbEntry());
        
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->client->token)
            ->seeStatusCode(403);
    }
    public function test_apply_conflictParticipantAlreadyInactive_applyNormally()
    {
        $inactiveParticipant = new RecordOfParticipant($this->program, $this->client, 0);
        $inactiveParticipant->active = false;
        $this->connection->table('Participant')->insert($inactiveParticipant->toArrayForDbEntry());
        
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->client->token)
            ->seeStatusCode(201);
    }
    
    public function test_cancel()
    {
        $this->connection->table('Registrant')->truncate();
        $this->programRegistrant->concluded = false;
        $this->connection->table('Registrant')->insert($this->programRegistrant->toArrayForDbEntry());
        
        $uri = $this->programRegistrationUri . "/{$this->programRegistrant->id}/cancel";
        $this->patch($uri, [], $this->client->token)
            ->seeStatusCode(200);
        $programRegistrantEntry = [
            "id" => $this->programRegistrant->id,
            "concluded" => true,
            "note" => 'cancelled',
        ];
        $this->seeInDatabase('Registrant', $programRegistrantEntry);
    }
    public function test_cancel_programRegistrationAlreadyConcluded_error403()
    {
        $uri = $this->programRegistrationUri . "/{$this->programRegistrant->id}/cancel";
        $this->patch($uri, [], $this->client->token)
            ->seeStatusCode(403);
    }
    
    public function test_show()
    {
        $response = [
            "id" => $this->programRegistrant->id,
            "program" => [
                "id" => $this->programRegistrant->program->id,
                "name" => $this->programRegistrant->program->name,
                "firm" => [
                    "id" => $this->programRegistrant->program->firm->id,
                    "name" => $this->programRegistrant->program->firm->name,
                ],
            ],
            "appliedTime" => $this->programRegistrant->appliedTime,
            "concluded" => $this->programRegistrant->concluded,
            "note" => $this->programRegistrant->note,
        ];
        
        $uri = $this->programRegistrationUri . "/{$this->programRegistrant->id}";
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
                    "id" => $this->programRegistrant->id,
                    "note" => $this->programRegistrant->note,
                    "concluded" => $this->programRegistrant->concluded,
                    "program" => [
                        "id" => $this->programRegistrant->program->id,
                        "name" => $this->programRegistrant->program->name,
                        "removed" => $this->programRegistrant->program->removed,
                    ],
                ],
                [
                    "id" => $this->programRegistrantOne->id,
                    "note" => $this->programRegistrantOne->note,
                    "concluded" => $this->programRegistrantOne->concluded,
                    "program" => [
                        "id" => $this->programRegistrantOne->program->id,
                        "name" => $this->programRegistrantOne->program->name,
                        "removed" => $this->programRegistrantOne->program->removed,
                    ],
                ],
            ],
        ];
        
        $this->get($this->programRegistrationUri, $this->client->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
    
}
