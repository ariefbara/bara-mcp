<?php

namespace Tests\Controllers\Client\TeamMembership;

use DateTimeImmutable;
use Query\Domain\Model\Firm\ParticipantTypes;
use Tests\Controllers\ {
    Client\AsTeamMember\AsTeamMemberTestCase,
    RecordPreparation\Firm\Program\RecordOfParticipant,
    RecordPreparation\Firm\Program\RecordOfRegistrant,
    RecordPreparation\Firm\Program\RecordOfRegistrationPhase,
    RecordPreparation\Firm\RecordOfProgram,
    RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation,
    RecordPreparation\Firm\Team\RecordOfTeamProgramRegistration
};

class ProgramRegistrationControllerTest extends AsTeamMemberTestCase
{
    protected $programRegistrationUri;
    protected $programRegistration, $concludedProgramRegistration;
    protected $programParticipation;


    protected $program, $registrationPhase;
    
    protected $registerInput = [];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->programRegistrationUri = $this->asTeamMemberUri . "/program-registrations";
        $this->connection->table('Program')->truncate();
        $this->connection->table('RegistrationPhase')->truncate();
        $this->connection->table('Registrant')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('TeamRegistrant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        
        $team = $this->teamMember->team;
        
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
        
        $this->programRegistration = new RecordOfTeamProgramRegistration($team, $registrant);
        $this->concludedProgramRegistration = new RecordOfTeamProgramRegistration($team, $concludedRegistrant);
        $this->connection->table('TeamRegistrant')->insert($this->programRegistration->toArrayForDbEntry());
        $this->connection->table('TeamRegistrant')->insert($this->concludedProgramRegistration->toArrayForDbEntry());
        
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
        $this->connection->table('TeamRegistrant')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
    }
    
    public function test_register_201()
    {
        $this->connection->table('Registrant')->truncate();
        $this->connection->table('TeamRegistrant')->truncate();
        
        $response = [
            "program" => [
                "id" => $this->program->id,
                "name" => $this->program->name,
            ],
            "registeredTime" => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            "concluded" => false,
            "note" => null,
        ];
        
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->teamMember->client->token)
            ->seeStatusCode(201)
            ->seeJsonContains($response);
        
        $registrantEntry = [
            'Program_id' => $this->program->id,
            "registeredTime" => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            "concluded" => false,
            "note" => null,
        ];
        $this->seeInDatabase('Registrant', $registrantEntry);
        $teamRegistrantEntry = [
            'Team_id' => $this->teamMember->team->id,
        ];
        $this->seeInDatabase('TeamRegistrant', $teamRegistrantEntry);
    }
    public function test_register_noOpenRegistrationPhaseAvailable_403()
    {
        $this->connection->table('RegistrationPhase')->truncate();
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->teamMember->client->token)
            ->seeStatusCode(403);
    }
    public function test_register_noTeamTypeInProgramParticipantTypesList_403()
    {
        $this->connection->table('Program')->truncate();
        $this->program->participantTypes = ParticipantTypes::USER_TYPE;
        $this->connection->table('Program')->insert($this->program->toArrayForDbEntry());
        
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->teamMember->client->token)
            ->seeStatusCode(403);
    }
    public function test_register_removedProgram_404()
    {
        $this->connection->table('Program')->truncate();
        $this->program->removed = true;
        $this->connection->table('Program')->insert($this->program->toArrayForDbEntry());
        
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->teamMember->client->token)
            ->seeStatusCode(404);
    }
    public function test_register_alreadyRegistedInProgram_403()
    {
        $registrant = new RecordOfRegistrant($this->program, 3);
        $teamProgramRegistration = new RecordOfTeamProgramRegistration($this->teamMember->team, $registrant);
        $this->connection->table('Registrant')->insert($registrant->toArrayForDbEntry());
        $this->connection->table('TeamRegistrant')->insert($teamProgramRegistration->toArrayForDbEntry());
        
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->teamMember->client->token)
            ->seeStatusCode(403);
    }
    public function test_register_existingRegistrationAlreadyConcluded_201()
    {
        $registrant = new RecordOfRegistrant($this->program, 3);
        $registrant->concluded = true;
        $teamProgramRegistration = new RecordOfTeamProgramRegistration($this->teamMember->team, $registrant);
        $this->connection->table('Registrant')->insert($registrant->toArrayForDbEntry());
        $this->connection->table('TeamRegistrant')->insert($teamProgramRegistration->toArrayForDbEntry());
        
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->teamMember->client->token)
            ->seeStatusCode(201);
    }
    public function test_register_inactiveMember_403()
    {
        $this->setTeamMembershipInactive();
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->teamMember->client->token)
            ->seeStatusCode(403);
    }
    public function test_register_alreadyParticipateInProgram_403()
    {
        $participant = new RecordOfParticipant($this->program, 0);
        $teamProgramParticipation = new RecordOfTeamProgramParticipation($this->teamMember->team, $participant);
        $this->connection->table('Participant')->insert($participant->toArrayForDbEntry());
        $this->connection->table('TeamParticipant')->insert($teamProgramParticipation->toArrayForDbEntry());
        
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->teamMember->client->token)
            ->seeStatusCode(403);
    }
    public function test_register_conflictParticipantAlreadyInactive_201()
    {
        $participant = new RecordOfParticipant($this->program, 0);
        $participant->active = false;
        $teamProgramParticipation = new RecordOfTeamProgramParticipation($this->teamMember->team, $participant);
        $this->connection->table('Participant')->insert($participant->toArrayForDbEntry());
        $this->connection->table('TeamParticipant')->insert($teamProgramParticipation->toArrayForDbEntry());
        
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->teamMember->client->token)
            ->seeStatusCode(201);
    }
    
    public function test_cancel_200()
    {
        $uri = $this->programRegistrationUri . "/{$this->programRegistration->id}/cancel";
        $this->patch($uri, [], $this->teamMember->client->token)
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
        $this->patch($uri, [], $this->teamMember->client->token)
            ->seeStatusCode(403);
    }
    public function test_cancel_inactiveMember_403()
    {
        $this->setTeamMembershipInactive();
        $uri = $this->programRegistrationUri . "/{$this->programRegistration->id}/cancel";
        $this->patch($uri, [], $this->teamMember->client->token)
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
        $this->get($uri, $this->teamMember->client->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
    public function test_show_inactiveMember_403()
    {
        $this->setTeamMembershipInactive();
        $uri = $this->programRegistrationUri . "/{$this->programRegistration->id}";
        $this->get($uri, $this->teamMember->client->token)
            ->seeStatusCode(403);
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
        
        $this->get($this->programRegistrationUri, $this->teamMember->client->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
    public function test_showAll_inactiveMember_403()
    {
        $this->setTeamMembershipInactive();
        $this->get($this->programRegistrationUri, $this->teamMember->client->token)
            ->seeStatusCode(403);
    }
}
