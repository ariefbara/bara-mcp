<?php

namespace Tests\Controllers\Client;

use DateTime;
use SharedContext\Domain\ValueObject\RegistrationStatus;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientRegistrant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfRegistrant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfRegistrationPhase;
use Tests\Controllers\RecordPreparation\Firm\Program\Registrant\RecordOfRegistrantInvoice;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\RecordOfFirm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfInvoice;

class ProgramRegistrationControllerTest extends ExtendedClientControllerTestCase
{
    protected $programRegistrationOne;
    protected $programRegistrationTwo;
    protected $programRegistrationUri;
    //
    protected $program;
    protected $programRegistrationPhase;
    protected $registerRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Program')->truncate();
        $this->connection->table('RegistrationPhase')->truncate();
        $this->connection->table('Registrant')->truncate();
        $this->connection->table('ClientRegistrant')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('Invoice')->truncate();
        $this->connection->table('RegistrantInvoice')->truncate();
        
        $this->programRegistrationUri = "/api/client/program-registrations";
        
        $firm = $this->client->firm;
        
        $programOne = new RecordOfProgram($firm, '1');
        $programTwo = new RecordOfProgram($firm, '2');
        $this->program = new RecordOfProgram($firm, '0');
        
        $this->programRegistrationPhase = new RecordOfRegistrationPhase($this->program, '0');
        
        $registrantOne = new RecordOfRegistrant($programOne, '1');
        $registrantTwo = new RecordOfRegistrant($programTwo, '2');
        
        $this->programRegistrationOne = new RecordOfClientRegistrant($this->client, $registrantOne);
        $this->programRegistrationTwo = new RecordOfClientRegistrant($this->client, $registrantTwo);
        
        $this->registerRequest = [
            'programId' => $this->program->id,
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
        $this->connection->table('Invoice')->truncate();
        $this->connection->table('RegistrantInvoice')->truncate();
    }
    
    protected function register()
    {
        $this->persistClientDependency();
        $this->program->insert($this->connection);
        $this->programRegistrationPhase->insert($this->connection);
        
        $this->post($this->programRegistrationUri, $this->registerRequest, $this->client->token);
    }
    public function test_register_201()
    {
$this->disableExceptionHandling();
        $this->register();
        $this->seeStatusCode(201);
        
        $response = [
            'status' => 'REGISTERED',
            'programSnapshot' => [
                "price" => $this->program->price,
                "autoAccept" => $this->program->autoAccept,
            ],
            "program" => [
                "id" => $this->program->id,
                "name" =>  $this->program->name,
                "removed" =>  $this->program->removed,
                "sponsors" => [],
            ],
        ];
        $this->seeJsonContains($response);
        
        $clientRegistrantEntry = [
            'Client_id' => $this->client->id,
        ];
        $this->seeInDatabase('ClientRegistrant', $clientRegistrantEntry);
        
        $registrantEntry = [
            'Program_id' => $this->program->id,
            'status' => RegistrationStatus::REGISTERED,
        ];
        $this->seeInDatabase('Registrant', $registrantEntry);
    }
    public function test_register_paidAutoAcceptProgram_setStatusSettlemenRequired_201()
    {
$this->disableExceptionHandling();
        $this->program->price = 300000;
        $this->program->autoAccept = true;
        
        $this->register();
        $this->seeStatusCode(201);
        
        $this->seeJsonContains(['status' => 'SETTLEMENT_REQUIRED']);
        $this->seeInDatabase('Registrant', ['status' => RegistrationStatus::SETTLEMENT_REQUIRED]);
    }
    public function test_register_paidAutoAcceptProgram_generateInvoice_201()
    {
$this->disableExceptionHandling();
        $this->program->price = 300000;
        $this->program->autoAccept = true;
        
        $this->register();
        $this->seeStatusCode(201);
        
        $this->seeInDatabase('Invoice', ['settled' => false]);
    }
    public function test_register_paidManualAcceptProgram_201()
    {
        $this->program->price = 300000;
        
        $this->register();
        $this->seeStatusCode(201);
        
        $response = [
            'status' => 'REGISTERED',
            'programSnapshot' => [
                "price" => $this->program->price,
                "autoAccept" => $this->program->autoAccept,
            ],
            "program" => [
                "id" => $this->program->id,
                "name" =>  $this->program->name,
                "removed" =>  $this->program->removed,
                "sponsors" => [],
            ],
        ];
        $this->seeJsonContains($response);
        
        $clientRegistrantEntry = [
            'Client_id' => $this->client->id,
        ];
        $this->seeInDatabase('ClientRegistrant', $clientRegistrantEntry);
        
        $registrantEntry = [
            'Program_id' => $this->program->id,
            'status' => RegistrationStatus::REGISTERED,
            "Program_price" => $this->program->price,
            "Program_autoAccept" => $this->program->autoAccept,
        ];
        $this->seeInDatabase('Registrant', $registrantEntry);
    }
    public function test_register_freeAutoAcceptProgram_acceptAsParticipant()
    {
$this->disableExceptionHandling();
        $this->program->autoAccept = true;
        
        $this->register();
        $this->seeStatusCode(201);
        
        $response = [
            'note' => null,
            'active' => true,
            "program" => [
                "id" => $this->program->id,
                "name" =>  $this->program->name,
                "removed" =>  $this->program->removed,
                "sponsors" => [],
            ],
        ];
        $this->seeJsonContains($response);
        
        $clientParticipantEntry = [
            'Client_id' => $this->client->id,
        ];
        $this->seeInDatabase('ClientParticipant', $clientParticipantEntry);
        
        $participantEntry = [
            'Program_id' => $this->program->id,
            'active' => true,
        ];
        $this->seeInDatabase('Participant', $participantEntry);
    }
    public function test_register_unpublishProgram_403()
    {
        $this->program->published = false;
        
        $this->register();
        $this->seeStatusCode(403);
    }
    public function test_register_noOpenRegistrationPhase_403()
    {
        $this->programRegistrationPhase->startDate = (new DateTime('+1 weeks'))->format('Y-m-d');
        $this->programRegistrationPhase->endDate = (new DateTime('+3 weeks'))->format('Y-m-d');
        
        $this->register();
        $this->seeStatusCode(403);
    }
    public function test_register_unsuportedClientType_403()
    {
        $this->program->participantTypes = 'team';
        
        $this->register();
        $this->seeStatusCode(403);
    }
    public function test_register_unuseableProgram_403()
    {
        $otherFirm = new RecordOfFirm('other');
        $otherFirm->insert($this->connection);
        $this->program->firm = $otherFirm;
        
        $this->register();
        $this->seeStatusCode(403);
    }
    public function test_register_hasUnconcludedRegistrationInProgram_403()
    {
        $this->programRegistrationOne->registrant->program = $this->program;
        $this->programRegistrationOne->insert($this->connection);
        
        $this->register();
        $this->seeStatusCode(403);
    }
    public function test_register_onlyUnconcludedRegistrationInProgram_201()
    {
        $this->programRegistrationOne->registrant->status = RegistrationStatus::REJECTED;
        $this->programRegistrationOne->registrant->program = $this->program;
        $this->programRegistrationOne->insert($this->connection);
        
        $this->register();
        $this->seeStatusCode(201);
    }
    public function test_register_isActiveParticipantInProgram_403()
    {
        $participant = new RecordOfParticipant($this->program, '0');
        $clientParticipant = new RecordOfClientParticipant($this->client, $participant);
        $clientParticipant->insert($this->connection);
        
        $this->register();
        $this->seeStatusCode(403);
    }
    public function test_register_onlyInactiveParticipantInProgram_201()
    {
        $participant = new RecordOfParticipant($this->program, '0');
        $participant->active = false;
        $clientParticipant = new RecordOfClientParticipant($this->client, $participant);
        $clientParticipant->insert($this->connection);
        
        $this->register();
        $this->seeStatusCode(201);
    }
    public function test_register_inactiveClient()
    {
        $this->client->activated = false;
        
        $this->register();
        $this->seeStatusCode(403);
    }
    
    protected function show()
    {
        $this->persistClientDependency();
        $this->programRegistrationOne->registrant->program->insert($this->connection);
        $this->programRegistrationOne->insert($this->connection);
        
        $uri = $this->programRegistrationUri . "/{$this->programRegistrationOne->id}";
        $this->get($uri, $this->client->token);
    }
    public function test_show_200()
    {
        $this->show();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->programRegistrationOne->id,
            'status' => 'REGISTERED',
            'programSnapshot' => [
                'price' => $this->programRegistrationOne->registrant->programPrice,
                'autoAccept' => $this->programRegistrationOne->registrant->programAutoAccept,
            ],
            "program" => [
                "id" => $this->programRegistrationOne->registrant->program->id,
                "name" => $this->programRegistrationOne->registrant->program->name,
                "removed" => $this->programRegistrationOne->registrant->program->removed,
                "sponsors" => [],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_show_hasInvoice_200()
    {
        $invoice = new RecordOfInvoice('0');
        $registrantInvoice = new RecordOfRegistrantInvoice($this->programRegistrationOne->registrant, $invoice);
        $registrantInvoice->insert($this->connection);
        
        $this->show();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->programRegistrationOne->id,
            'status' => 'REGISTERED',
            'programSnapshot' => [
                'price' => $this->programRegistrationOne->registrant->programPrice,
                'autoAccept' => $this->programRegistrationOne->registrant->programAutoAccept,
            ],
            "program" => [
                "id" => $this->programRegistrationOne->registrant->program->id,
                "name" => $this->programRegistrationOne->registrant->program->name,
                "removed" => $this->programRegistrationOne->registrant->program->removed,
                "sponsors" => [],
            ],
            'invoice' => [
                'issuedTime' => $registrantInvoice->invoice->issuedTime,
                'expiredTime' => $registrantInvoice->invoice->expiredTime,
                'paymentLink' => $registrantInvoice->invoice->paymentLink,
                'settled' => $registrantInvoice->invoice->settled,
            ],
        ];
        $this->seeJsonContains($response);
    }
    
    protected function showAll()
    {
        $this->persistClientDependency();
        
        $this->programRegistrationOne->registrant->program->insert($this->connection);
        $this->programRegistrationTwo->registrant->program->insert($this->connection);
        
        $this->programRegistrationOne->insert($this->connection);
        $this->programRegistrationTwo->insert($this->connection);
        
        $this->get($this->programRegistrationUri, $this->client->token);
    }
    public function test_showAll_200()
    {
        $this->showAll();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => 2,
            'list' => [
                [
                    'id' => $this->programRegistrationOne->id,
                    'registeredTime' => $this->programRegistrationOne->registrant->registeredTime,
                    'status' => 'REGISTERED',
                    'programSnapshot' => [
                        'price' => $this->programRegistrationOne->registrant->programPrice,
                        'autoAccept' => $this->programRegistrationOne->registrant->programAutoAccept,
                    ],
                    "program" => [
                        "id" => $this->programRegistrationOne->registrant->program->id,
                        "name" => $this->programRegistrationOne->registrant->program->name,
                        "removed" => $this->programRegistrationOne->registrant->program->removed,
                        "sponsors" => [],
                    ],
                    'invoice' => null,
                ],
                [
                    'id' => $this->programRegistrationTwo->id,
                    'registeredTime' => $this->programRegistrationTwo->registrant->registeredTime,
                    'status' => 'REGISTERED',
                    'invoice' => null,
                    'programSnapshot' => [
                        'price' => $this->programRegistrationTwo->registrant->programPrice,
                        'autoAccept' => $this->programRegistrationTwo->registrant->programAutoAccept,
                    ],
                    "program" => [
                        "id" => $this->programRegistrationTwo->registrant->program->id,
                        "name" => $this->programRegistrationTwo->registrant->program->name,
                        "removed" => $this->programRegistrationTwo->registrant->program->removed,
                        "sponsors" => [],
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
}
