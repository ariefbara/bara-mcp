<?php

namespace Tests\Controllers\Client;

use DateTime;
use DateTimeImmutable;
use Query\Domain\Model\Firm\ParticipantTypes;
use SharedContext\Domain\ValueObject\RegistrationStatus;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientRegistrant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfProgramsProfileForm;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfRegistrant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfRegistrationPhase;
use Tests\Controllers\RecordPreparation\Firm\RecordOfFirmFileInfo;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProfileForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFileInfo;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;

class ProgramRegistrationControllerTest extends ClientTestCase
{
    protected $programRegistrationUri;
    protected $clientRegistrant;
    protected $clientParticipant;

    protected $firmFileInfo;

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
        $this->connection->table('FirmFileInfo')->truncate();
        $this->connection->table('FileInfo')->truncate();
        
        $firm = $this->client->firm;
        
        $fileInfo = new RecordOfFileInfo("2");
        $this->firmFileInfo = new RecordOfFirmFileInfo($firm, $fileInfo);
                
        $this->program = new RecordOfProgram($this->client->firm, 0);
        $this->program->illustration = $this->firmFileInfo;
        $this->registrationPhase = new RecordOfRegistrationPhase($this->program, 0);
        
        $registrant = new RecordOfRegistrant($this->program, 0);
        $this->clientRegistrant = new RecordOfClientRegistrant($this->client, $registrant);
        $participant = new RecordOfParticipant($this->program, '1');
        $this->clientParticipant = new RecordOfClientParticipant($this->client, $participant);
        
        $formOne = new RecordOfForm('1');
        
        $profileFormOne = new RecordOfProfileForm($firm, $formOne);
        
        $this->programsProfileFormOne = new RecordOfProgramsProfileForm($this->program, $profileFormOne, '1');
        
        $this->registrationInput = [
            "programId" => $this->program->id,
        ];
    }
    
    protected function tearDown(): void
    {
//        parent::tearDown();
//        $this->connection->table('Program')->truncate();
//        $this->connection->table('RegistrationPhase')->truncate();
//        $this->connection->table('Registrant')->truncate();
//        $this->connection->table('ClientRegistrant')->truncate();
//        $this->connection->table('Participant')->truncate();
//        $this->connection->table('ClientParticipant')->truncate();
//        $this->connection->table('Form')->truncate();
//        $this->connection->table('ProfileForm')->truncate();
//        $this->connection->table('ProgramsProfileForm')->truncate();
//        $this->connection->table('FirmFileInfo')->truncate();
//        $this->connection->table('FileInfo')->truncate();
    }
    
    protected function register()
    {
        $this->registrationPhase->insert($this->connection);
        $this->program->insert($this->connection);
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->client->token);
    }
    public function test_register_201()
    {
        $this->register();
        $this->seeStatusCode(201);
        $response = [
            "status" => 'REGISTERED',
        ];
        $this->seeJsonContains($response);
        
        $registrantEntry = [
            'Program_id' => $this->program->id,
            "registeredTime" => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            "status" => RegistrationStatus::REGISTERED,
        ];
        $this->seeInDatabase('Registrant', $registrantEntry);
        $clientRegistrantEntry = [
            'Client_id' => $this->client->id,
        ];
        $this->seeInDatabase('ClientRegistrant', $clientRegistrantEntry);
    }
    public function test_register_noOpenRegistrationPhaseAvailable_403()
    {
        $this->registrationPhase->startDate = (new DateTime('+2 days'))->format('Y-m-d H:i:s');
        $this->registrationPhase->endDate = (new DateTime('+7 days'))->format('Y-m-d H:i:s');
        $this->register();
        $this->seeStatusCode(403);
    }
    public function test_register_unpublishedProgram_403()
    {
        $this->program->published = false;
        $this->register();
        $this->seeStatusCode(403);
    }
    public function test_register_noClientTypeInProgramParticipantTypesList_403()
    {
        $this->program->participantTypes = ParticipantTypes::USER_TYPE;
        $this->register();
        $this->seeStatusCode(403);
    }
    public function test_register_removedProgram_404()
    {
        $this->program->removed = true;
        $this->register();
        $this->seeStatusCode(404);
    }
    public function test_register_alreadyRegistedInProgram_403()
    {
        $this->clientRegistrant->insert($this->connection);
        $this->register();
        $this->seeStatusCode(403);
    }
    public function test_register_existingRegistrationAlreadyConcluded_201()
    {
        $this->clientRegistrant->registrant->status = RegistrationStatus::ACCEPTED;
        $this->clientRegistrant->insert($this->connection);
        $this->register();
        $this->seeStatusCode(201);
    }
    public function test_register_isActiveParticipantOfProgram_403()
    {
        $this->clientParticipant->insert($this->connection);
        $this->register();
        $this->seeStatusCode(403);
    }
    public function test_register_isInactiveParticipantOfProgram_201()
    {
        $this->clientParticipant->participant->active = false;
        $this->clientParticipant->insert($this->connection);
        $this->register();
        $this->seeStatusCode(201);
    }
    public function test_register_inactiveClient_403()
    {
        $this->post($this->programRegistrationUri, $this->registrationInput, $this->inactiveClient->token)
            ->seeStatusCode(403);
    }
    public function test_register_autoAcceptAndPaidProgram_setRegistrationStatusAsSettlementRequired()
    {
        $this->program->autoAccept = true;
        $this->program->price = 100000;
        $this->register();
        $this->seeStatusCode(201);
        
        $registrantRecord = [
            'status' => RegistrationStatus::SETTLEMENT_REQUIRED,
            'Program_id' => $this->program->id,
        ];
        $this->seeInDatabase('Registrant', $registrantRecord);
    }
    public function test_register_autoAcceptAndFreeProgram_setParticipant()
    {
        $this->program->autoAccept = true;
        $this->register();
        $this->seeStatusCode(201);
        
        $participantRecord = [
            'Program_id' => $this->program->id,
            'active' => true,
        ];
        $this->seeInDatabase('Participant', $participantRecord);
        
        $clientParticipantRecord = [
            'Client_id' => $this->client->id,
        ];
        $this->seeInDatabase('ClientParticipant', $clientParticipantRecord);
$this->response->dump();
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
                "illustration" => null,
                "programType" => $this->programRegistration->registrant->program->programType,
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
                        "illustration" => null,
                        "programType" => $this->programRegistration->registrant->program->programType,
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
                        "programType" => $this->concludedProgramRegistration->registrant->program->programType,
                        "hasProfileForm" => false,
                        "illustration" => [
                            "id" => $this->firmFileInfoTwo->id,
                            "url" => "/{$this->firmFileInfoTwo->fileInfo->name}",
                        ],
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
                        "programType" => $this->programRegistration->registrant->program->programType,
                        "hasProfileForm" => false,
                        "illustration" => null,
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
