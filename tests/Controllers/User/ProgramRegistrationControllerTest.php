<?php

namespace Tests\Controllers\User;

use DateTimeImmutable;
use Query\Domain\Model\Firm\ParticipantTypes;
use Tests\Controllers\RecordPreparation\ {
    Firm\Program\RecordOfParticipant,
    Firm\Program\RecordOfRegistrant,
    Firm\Program\RecordOfRegistrationPhase,
    Firm\RecordOfProgram,
    RecordOfFirm,
    User\RecordOfUserParticipant,
    User\RecordOfUserRegistrant
};

class ProgramRegistrationControllerTest extends UserTestCase
{
    protected $programRegistrationUri;
    protected $programRegistration, $concludedProgramRegistration;
    protected $programParticipation;


    protected $program, $registrationPhase;
    
    protected $registerInput = [];

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->programRegistrationUri = $this->userUri. "/program-registrations";
        
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('RegistrationPhase')->truncate();
        $this->connection->table('Registrant')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('UserRegistrant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        
        $firm = new RecordOfFirm(0, 'firm-identifier');
        $this->connection->table('Firm')->insert($firm->toArrayForDbEntry());
        
        $this->program = new RecordOfProgram($firm, 0);
        $programOne = new RecordOfProgram($firm, 1);
        $programTwo = new RecordOfProgram($firm, 2);
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
        
        $this->programRegistration = new RecordOfUserRegistrant($this->user, $registrant);
        $this->concludedProgramRegistration = new RecordOfUserRegistrant($this->user, $concludedRegistrant);
        $this->connection->table('UserRegistrant')->insert($this->programRegistration->toArrayForDbEntry());
        $this->connection->table('UserRegistrant')->insert($this->concludedProgramRegistration->toArrayForDbEntry());
        
        $this->registrationInput = [
            'firmId' => $this->program->firm->id,
            "programId" => $this->program->id,
        ];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('RegistrationPhase')->truncate();
        $this->connection->table('Registrant')->truncate();
        $this->connection->table('UserRegistrant')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
    }
    public function test_register_201()
    {
        $response = [
            "program" => [
                "id" => $this->program->id,
                "name" => $this->program->name,
                'firm' => [
                    "id" => $this->program->firm->id,
                    "name" => $this->program->firm->name,
                ],
            ],
            "registeredTime" => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            "concluded" => false,
            "note" => null,
        ];
        
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->user->token)
            ->seeStatusCode(201)
            ->seeJsonContains($response);
        
        $registrantEntry = [
            'Program_id' => $this->program->id,
            "registeredTime" => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            "concluded" => false,
            "note" => null,
        ];
        $this->seeInDatabase('Registrant', $registrantEntry);
    }
    public function test_register_noOpenRegistrationPhaseAvailable_403()
    {
        $this->connection->table('RegistrationPhase')->truncate();
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->user->token)
            ->seeStatusCode(403);
    }
    public function test_register_noUserTypeInProgramParticipantTypesList_403()
    {
        $this->connection->table('Program')->truncate();
        $this->program->participantTypes = ParticipantTypes::CLIENT_TYPE;
        $this->connection->table('Program')->insert($this->program->toArrayForDbEntry());
        
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->user->token)
            ->seeStatusCode(403);
    }
    public function test_register_removedProgram_404()
    {
        $this->connection->table('Program')->truncate();
        $this->program->removed = true;
        $this->connection->table('Program')->insert($this->program->toArrayForDbEntry());
        
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->user->token)
            ->seeStatusCode(404);
    }
    public function test_register_alreadyRegistedInProgram_403()
    {
        $registrant = new RecordOfRegistrant($this->program, 3);
        $userRegistrant = new RecordOfUserRegistrant($this->user, $registrant);
        $this->connection->table('Registrant')->insert($registrant->toArrayForDbEntry());
        $this->connection->table('UserRegistrant')->insert($userRegistrant->toArrayForDbEntry());
        
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->user->token)
            ->seeStatusCode(403);
    }
    public function test_register_existingRegistrationAlreadyConcluded_201()
    {
        $registrant = new RecordOfRegistrant($this->program, 3);
        $registrant->concluded = true;
        $userRegistrant = new RecordOfUserRegistrant($this->user, $registrant);
        $this->connection->table('Registrant')->insert($registrant->toArrayForDbEntry());
        $this->connection->table('UserRegistrant')->insert($userRegistrant->toArrayForDbEntry());
        
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->user->token)
            ->seeStatusCode(201);
    }
    public function test_register_inactiveUser_403()
    {
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->inactiveUser->token)
            ->seeStatusCode(403);
    }
    public function test_register_alreadyParticipateInProgram_403()
    {
        $participant = new RecordOfParticipant($this->program, 0);
        $userParticipant = new RecordOfUserParticipant($this->user, $participant);
        $this->connection->table('Participant')->insert($participant->toArrayForDbEntry());
        $this->connection->table('UserParticipant')->insert($userParticipant->toArrayForDbEntry());
        
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->user->token)
            ->seeStatusCode(403);
    }
    public function test_apply_conflictedParticipationAlreadyInactive_201()
    {
        $participant = new RecordOfParticipant($this->program, 0);
        $participant->active = false;
        $userParticipant = new RecordOfUserParticipant($this->user, $participant);
        $this->connection->table('Participant')->insert($participant->toArrayForDbEntry());
        $this->connection->table('UserParticipant')->insert($userParticipant->toArrayForDbEntry());
        
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->user->token)
            ->seeStatusCode(201);
    }
    
    public function test_cancel_200()
    {
        $uri = $this->programRegistrationUri . "/{$this->programRegistration->id}/cancel";
        $this->patch($uri, [], $this->user->token)
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
        $this->patch($uri, [], $this->user->token)
            ->seeStatusCode(403);
    }
    
    public function test_show()
    {
        $response = [
            "id" => $this->programRegistration->id,
            "program" => [
                "id" => $this->programRegistration->registrant->program->id,
                "name" => $this->programRegistration->registrant->program->name,
                'firm' => [
                    "id" => $this->programRegistration->registrant->program->firm->id,
                    "name" => $this->programRegistration->registrant->program->firm->name,
                ],
            ],
            "registeredTime" => $this->programRegistration->registrant->registeredTime,
            "concluded" => $this->programRegistration->registrant->concluded,
            "note" => $this->programRegistration->registrant->note,
        ];
        
        $uri = $this->programRegistrationUri . "/{$this->programRegistration->id}";
        $this->get($uri, $this->user->token)
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
                        'firm' => [
                            "id" => $this->programRegistration->registrant->program->firm->id,
                            "name" => $this->programRegistration->registrant->program->firm->name,
                        ],
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
                        'firm' => [
                            "id" => $this->concludedProgramRegistration->registrant->program->firm->id,
                            "name" => $this->concludedProgramRegistration->registrant->program->firm->name,
                        ],
                    ],
                    "registeredTime" => $this->concludedProgramRegistration->registrant->registeredTime,
                    "concluded" => $this->concludedProgramRegistration->registrant->concluded,
                    "note" => $this->concludedProgramRegistration->registrant->note,
                ],
            ],
        ];
        
        $this->get($this->programRegistrationUri, $this->user->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
}
