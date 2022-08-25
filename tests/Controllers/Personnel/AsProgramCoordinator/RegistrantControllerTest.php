<?php

namespace Tests\Controllers\Personnel\AsProgramCoordinator;

use SharedContext\Domain\ValueObject\RegistrationStatus;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientRegistrant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfRegistrant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramRegistration;

class RegistrantControllerTest extends ExtendedAsProgramCoordinatorTestCase
{
    protected $clientRegistrant;
    protected $teamRegistrant;
    protected $registrantUri;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        //
        $this->connection->table('Registrant')->truncate();
        $this->connection->table('ClientRegistrant')->truncate();
        $this->connection->table('TeamRegistrant')->truncate();
        //
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        //
        $this->connection->table('Invoice')->truncate();
        $this->connection->table('RegistrantInvoice')->truncate();
        
        $this->registrantUri = "/api/personnel/as-program-coordinator/{$this->coordinatorOne->program->id}/registrants";
        
        $program = $this->coordinatorOne->program;
        $firm = $program->firm;
        
        $clientOne = new RecordOfClient($firm, '1');
        
        $teamOne = new RecordOfTeam($firm, $clientOne, '1');
        
        $registrantOne = new RecordOfRegistrant($program, '1');
        $registrantTwo = new RecordOfRegistrant($program, '2');
        
        $this->clientRegistrant = new RecordOfClientRegistrant($clientOne, $registrantOne);
        
        $this->teamRegistrant = new RecordOfTeamProgramRegistration($teamOne, $registrantTwo);
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        //
        $this->connection->table('Registrant')->truncate();
        $this->connection->table('ClientRegistrant')->truncate();
        $this->connection->table('TeamRegistrant')->truncate();
        //
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        //
        $this->connection->table('Invoice')->truncate();
        $this->connection->table('RegistrantInvoice')->truncate();
    }
    
    protected function acceptTeamRegistrant()
    {
        $this->persistCoordinatorDependency();
        $this->teamRegistrant->team->insert($this->connection);
        $this->teamRegistrant->insert($this->connection);
        
        $uri = $this->registrantUri . "/{$this->teamRegistrant->registrant->id}/accept";
        $this->patch($uri, [], $this->coordinatorOne->personnel->token);
    }
    public function test_acceptTeamRegistrant_setAccepted_200()
    {
$this->disableExceptionHandling();
        $this->acceptTeamRegistrant();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->teamRegistrant->registrant->id,
            'status' => 'ACCEPTED',
        ];
        $this->seeJsonContains($response);
        
        $registrantEntry = [
            'id' => $this->teamRegistrant->registrant->id,
            'status' => RegistrationStatus::ACCEPTED,
        ];
        $this->seeInDatabase('Registrant', $registrantEntry);
    }
    public function test_acceptTeamRegistrant_addAsParticipant_200()
    {
        $this->acceptTeamRegistrant();
        $this->seeStatusCode(200);
        
        $participantEntry = [
            'Program_id' => $this->teamRegistrant->registrant->program->id,
            'active' => true,
        ];
        $this->seeInDatabase('Participant', $participantEntry);
        
        $teamParticipantEntry = [
            'Team_id' => $this->teamRegistrant->team->id,
        ];
        $this->seeInDatabase('TeamParticipant', $teamParticipantEntry);
    }
    public function test_acceptTeamRegistrant_paidProgram_setSettlementRequiredStatus()
    {
        $this->teamRegistrant->registrant->programPrice = 400000;
        
        $this->acceptTeamRegistrant();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->teamRegistrant->registrant->id,
            'status' => 'SETTLEMENT_REQUIRED',
        ];
        $this->seeJsonContains($response);
        
        $registrantEntry = [
            'id' => $this->teamRegistrant->registrant->id,
            'status' => RegistrationStatus::SETTLEMENT_REQUIRED,
        ];
        $this->seeInDatabase('Registrant', $registrantEntry);
    }
    public function test_acceptTeamRegistrant_paidProgram_generateInvoice()
    {
        $this->teamRegistrant->registrant->programPrice = 400000;
        
        $this->acceptTeamRegistrant();
        $this->seeStatusCode(200);
        
        $invoiceEntry = [
            'settled' => false,
        ];
        $this->seeInDatabase('Invoice', $invoiceEntry);
        
        $registrantInvoiceEntry = [
            'Registrant_id' => $this->teamRegistrant->registrant->id
        ];
        $this->seeInDatabase('RegistrantInvoice', $registrantInvoiceEntry);
    }
    public function test_accept_unmanagedRegistrant_belongsToOtherProgram_403()
    {
        $otherProgram = new RecordOfProgram($this->coordinatorOne->personnel->firm, 'other');
        $otherProgram->insert($this->connection);
        $this->teamRegistrant->registrant->program = $otherProgram;
        
        $this->acceptTeamRegistrant();
        $this->seeStatusCode(403);
    }
    public function test_accept_unmanagedRegistrant_inactiveCoordinator_403()
    {
        $this->coordinatorOne->active = false;
        
        $this->acceptTeamRegistrant();
        $this->seeStatusCode(403);
    }
    
    protected function acceptClientRegistrant()
    {
        $this->persistCoordinatorDependency();
        $this->clientRegistrant->client->insert($this->connection);
        $this->clientRegistrant->insert($this->connection);
        
        $uri = $this->registrantUri . "/{$this->clientRegistrant->registrant->id}/accept";
        $this->patch($uri, [], $this->coordinatorOne->personnel->token);
    }
    public function test_acceptClientRegistrant_setAccepted_200()
    {
$this->disableExceptionHandling();
        $this->acceptClientRegistrant();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->clientRegistrant->registrant->id,
            'status' => 'ACCEPTED',
        ];
        $this->seeJsonContains($response);
        
        $registrantEntry = [
            'id' => $this->clientRegistrant->registrant->id,
            'status' => RegistrationStatus::ACCEPTED,
        ];
        $this->seeInDatabase('Registrant', $registrantEntry);
    }
    public function test_acceptClientRegistrant_addAsParticipant_200()
    {
        $this->acceptClientRegistrant();
        $this->seeStatusCode(200);
        
        $participantEntry = [
            'Program_id' => $this->clientRegistrant->registrant->program->id,
            'active' => true,
        ];
        $this->seeInDatabase('Participant', $participantEntry);
        
        $clientParticipantEntry = [
            'Client_id' => $this->clientRegistrant->client->id,
        ];
        $this->seeInDatabase('ClientParticipant', $clientParticipantEntry);
    }
    public function test_acceptClientRegistrant_paidProgram_setSettlementRequiredStatus()
    {
        $this->clientRegistrant->registrant->programPrice = 400000;
        
        $this->acceptClientRegistrant();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->clientRegistrant->registrant->id,
            'status' => 'SETTLEMENT_REQUIRED',
        ];
        $this->seeJsonContains($response);
        
        $registrantEntry = [
            'id' => $this->clientRegistrant->registrant->id,
            'status' => RegistrationStatus::SETTLEMENT_REQUIRED,
        ];
        $this->seeInDatabase('Registrant', $registrantEntry);
    }
    public function test_acceptClientRegistrant_paidProgram_generateInvoice()
    {
        $this->clientRegistrant->registrant->programPrice = 400000;
        
        $this->acceptClientRegistrant();
        $this->seeStatusCode(200);
        
        $invoiceEntry = [
            'settled' => false,
        ];
        $this->seeInDatabase('Invoice', $invoiceEntry);
        
        $registrantInvoiceEntry = [
            'Registrant_id' => $this->clientRegistrant->registrant->id
        ];
        $this->seeInDatabase('RegistrantInvoice', $registrantInvoiceEntry);
    }
    
    protected function reject()
    {
        $this->persistCoordinatorDependency();
        $this->teamRegistrant->team->insert($this->connection);
        $this->teamRegistrant->insert($this->connection);
        
        $uri = $this->registrantUri . "/{$this->teamRegistrant->registrant->id}/reject";
        $this->patch($uri, [], $this->coordinatorOne->personnel->token);
    }
    public function test_reject_200()
    {
        $this->reject();
        $this->seeStatusCode(200);
        
        $registrantEntry = [
            "id" => $this->teamRegistrant->registrant->id,
            "status" => RegistrationStatus::REJECTED,
        ];
        $this->seeInDatabase("Registrant", $registrantEntry);
    }
    public function test_reject_inactiveCoordinator_403()
    {
        $this->coordinatorOne->active = false;
        
        $this->reject();
        $this->seeStatusCode(403);
    }
    
    protected function show()
    {
        $this->persistCoordinatorDependency();
        $this->teamRegistrant->team->insert($this->connection);
        $this->teamRegistrant->insert($this->connection);
        
        $uri = $this->registrantUri . "/{$this->teamRegistrant->registrant->id}";
        $this->get($uri, $this->coordinatorOne->personnel->token);
    }
    public function test_show_200()
    {
        $this->show();
        $this->seeStatusCode(200);
        
        $response = [
            "id" => $this->teamRegistrant->id,
            "registeredTime" => $this->teamRegistrant->registrant->registeredTime,
            "status" => 'REGISTERED',
            "team" => [
                "id" => $this->teamRegistrant->team->id,
                "name" => $this->teamRegistrant->team->name,
            ],
            "client" => null,
            "user" => null,
        ];
        $this->seeJsonContains($response);
    }
    
    protected function showAll()
    {
        $this->persistCoordinatorDependency();
        
        $this->teamRegistrant->team->insert($this->connection);
        $this->teamRegistrant->insert($this->connection);
        
        $this->clientRegistrant->client->insert($this->connection);
        $this->clientRegistrant->insert($this->connection);
        
        $this->get($this->registrantUri, $this->coordinatorOne->personnel->token);
    }
    public function test_showAll_200()
    {
        $this->showAll();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => 2,
            'list' => [
                [
                    "id" => $this->teamRegistrant->id,
                    "registeredTime" => $this->teamRegistrant->registrant->registeredTime,
                    "status" => 'REGISTERED',
                    "team" => [
                        "id" => $this->teamRegistrant->team->id,
                        "name" => $this->teamRegistrant->team->name,
                    ],
                    "client" => null,
                    "user" => null,
                ],
                [
                    "id" => $this->clientRegistrant->id,
                    "registeredTime" => $this->clientRegistrant->registrant->registeredTime,
                    "status" => 'REGISTERED',
                    "client" => [
                        "id" => $this->clientRegistrant->client->id,
                        "name" => $this->clientRegistrant->client->getFullName(),
                    ],
                    "team" => null,
                    "user" => null,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_show_concludedStatusFilterUsed_200()
    {
        $this->registrantUri .= '?concludedStatus=false';
        $this->clientRegistrant->registrant->status = RegistrationStatus::REJECTED;
        
        $this->showAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => 1]);
        
        $this->seeJsonContains(['id' => $this->teamRegistrant->registrant->id]);
        $this->seeJsonDoesntContains(['id' => $this->clientRegistrant->registrant->id]);
    }
    public function test_show_inactiveCoordinator_403()
    {
        $this->coordinatorOne->active = false;
        
        $this->showAll();
        $this->seeStatusCode(403);
    }
    
}
