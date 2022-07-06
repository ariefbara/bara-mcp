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
use Tests\Controllers\RecordPreparation\Firm\Program\Registrant\RecordOfRegistrantInvoice;
use Tests\Controllers\RecordPreparation\Firm\RecordOfFirmFileInfo;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProfileForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFileInfo;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfInvoice;

class ProgramRegistrationControllerTest extends ClientTestCase
{
    protected $programRegistrationUri;
    protected $clientRegistrant;
    protected $clientRegistrantTwo;
    protected $clientParticipant;
    protected $registrantInvoice;

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
        $this->connection->table('RegistrantInvoice')->truncate();
        $this->connection->table('Invoice')->truncate();
        
        $firm = $this->client->firm;
        
        $fileInfo = new RecordOfFileInfo("2");
        $this->firmFileInfo = new RecordOfFirmFileInfo($firm, $fileInfo);
                
        $this->program = new RecordOfProgram($this->client->firm, 0);
        $programTWo = new RecordOfProgram($this->client->firm, '2');
        $this->program->illustration = $this->firmFileInfo;
        $this->registrationPhase = new RecordOfRegistrationPhase($this->program, 0);
        
        $registrant = new RecordOfRegistrant($this->program, 0);
        $registrantTwo = new RecordOfRegistrant($programTWo, '2');
        $this->clientRegistrant = new RecordOfClientRegistrant($this->client, $registrant);
        $this->clientRegistrantTwo = new RecordOfClientRegistrant($this->client, $registrantTwo);
        
        $participant = new RecordOfParticipant($this->program, '1');
        $this->clientParticipant = new RecordOfClientParticipant($this->client, $participant);
        
        $invoice = new RecordOfInvoice('1');
        $this->registrantInvoice = new RecordOfRegistrantInvoice($registrant, $invoice);
        
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
$this->disableExceptionHandling();
        $this->register();
        $this->seeStatusCode(201);
        $response = [
            "status" => 'REGISTERED',
            'program' => [
                'id' => $this->program->id,
                'name' => $this->program->name,
                'programType' => $this->program->programType,
            ]
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
    public function test_register_autoAcceptAndPaidProgram_setRegistrationStatusAsSettlementRequiredAndSetInvoice()
    {
$this->disableExceptionHandling();
        $this->program->autoAccept = true;
        $this->program->price = 100000;
        $this->register();
        $this->seeStatusCode(201);
        
        $invoiceResponse = [
            'settled' => false,
        ];
        $this->seeJsonContains($invoiceResponse);
        
        $registrantEntry = [
            'status' => RegistrationStatus::SETTLEMENT_REQUIRED,
        ];
        $this->seeInDatabase('Registrant', $registrantEntry);
        
        $invoiceEntry = [
            'settled' => false,
        ];
        $this->seeInDatabase('Invoice', $invoiceEntry);
$this->response->dump();
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
    
    protected function cancel()
    {
        $this->clientRegistrant->registrant->program->insert($this->connection);
        $this->clientRegistrant->insert($this->connection);
        
        $uri = $this->programRegistrationUri . "/{$this->clientRegistrant->id}/cancel";
        $this->patch($uri, [], $this->client->token);
    }
    public function test_cancel_200()
    {
$this->disableExceptionHandling();
        $this->cancel();
        $this->seeStatusCode(200);
        
        $registrantEntry = [
            "id" => $this->clientRegistrant->registrant->id,
            "status" => RegistrationStatus::CANCELLED,
        ];
        $this->seeInDatabase('Registrant', $registrantEntry);
    }
    
    public function test_show()
    {
$this->disableExceptionHandling();
        $this->firmFileInfo->insert($this->connection);
        $this->clientRegistrant->registrant->program->insert($this->connection);
        $this->clientRegistrant->insert($this->connection);
        $this->registrantInvoice->insert($this->connection);
        $response = [
            "id" => $this->clientRegistrant->id,
            "program" => [
                "id" => $this->clientRegistrant->registrant->program->id,
                "name" => $this->clientRegistrant->registrant->program->name,
                "hasProfileForm" => false,
                "illustration" => [
                    'id' => $this->firmFileInfo->id,
                    'url' => $this->firmFileInfo->fileInfo->getFullyPath(),
                ],
                "programType" => $this->clientRegistrant->registrant->program->programType,
            ],
            "registeredTime" => $this->clientRegistrant->registrant->registeredTime,
            "status" => 'REGISTERED',
            "invoice" => [
                'issuedTime' => $this->registrantInvoice->invoice->issuedTime,
                'expiredTime' => $this->registrantInvoice->invoice->expiredTime,
                'paymentLink' => $this->registrantInvoice->invoice->paymentLink,
                'settled' => $this->registrantInvoice->invoice->settled,
            ],
        ];
        
        $uri = $this->programRegistrationUri . "/{$this->clientRegistrant->id}";
        $this->get($uri, $this->client->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
    
    protected function showAll()
    {
        $this->clientRegistrant->insert($this->connection);
        $this->clientRegistrant->registrant->program->insert($this->connection);
        $this->clientRegistrantTwo->insert($this->connection);
        $this->clientRegistrantTwo->registrant->program->insert($this->connection);
        $this->firmFileInfo->insert($this->connection);
        $this->registrantInvoice->insert($this->connection);
        
        $this->get($this->programRegistrationUri, $this->client->token);
    }
    public function test_showAll()
    {
        $this->showAll();
        $this->seeStatusCode(200);
        $response = [
            "total" => 2, 
            "list" => [
                [
                    "id" => $this->clientRegistrant->id,
                    "program" => [
                        "id" => $this->clientRegistrant->registrant->program->id,
                        "name" => $this->clientRegistrant->registrant->program->name,
                        "hasProfileForm" => false,
                        "illustration" => [
                            'id' => $this->firmFileInfo->id,
                            'url' => $this->firmFileInfo->fileInfo->getFullyPath(),
                        ],
                        "programType" => $this->clientRegistrant->registrant->program->programType,
                    ],
                    "registeredTime" => $this->clientRegistrant->registrant->registeredTime,
                    "status" => 'REGISTERED',
                    "invoice" => [
                        'issuedTime' => $this->registrantInvoice->invoice->issuedTime,
                        'expiredTime' => $this->registrantInvoice->invoice->expiredTime,
                        'paymentLink' => $this->registrantInvoice->invoice->paymentLink,
                        'settled' => $this->registrantInvoice->invoice->settled,
                    ],
                ],
                [
                    "id" => $this->clientRegistrantTwo->id,
                    "program" => [
                        "id" => $this->clientRegistrantTwo->registrant->program->id,
                        "name" => $this->clientRegistrantTwo->registrant->program->name,
                        "hasProfileForm" => false,
                        "illustration" => null,
                        "programType" => $this->clientRegistrant->registrant->program->programType,
                    ],
                    "registeredTime" => $this->clientRegistrant->registrant->registeredTime,
                    "status" => 'REGISTERED',
                    "invoice" => null,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_filterConcludedStatus()
    {
$this->disableExceptionHandling();
        $this->clientRegistrantTwo->registrant->status = 5;
        $this->programRegistrationUri .= '?concludedStatus=false';
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 1];
        $this->seeJsonContains($totalResponse);
        
        $clientRegistrantOneResponse = ['id' => $this->clientRegistrant->id];
        $this->seeJsonContains($clientRegistrantOneResponse);
        
        $clientRegistrantTwoResponse = ['id' => $this->clientRegistrantTwo->id];
        $this->seeJsonDoesntContains($clientRegistrantTwoResponse);
    }
}
