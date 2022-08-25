<?php

namespace Tests\Controllers;

use SharedContext\Domain\ValueObject\RegistrationStatus;
use Tests\Controllers\ControllerTestCase;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientRegistrant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfRegistrant;
use Tests\Controllers\RecordPreparation\Firm\Program\Registrant\RecordOfRegistrantInvoice;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramRegistration;
use Tests\Controllers\RecordPreparation\RecordOfFirm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfInvoice;

class XenditControllerTest extends ControllerTestCase
{
    protected $xenditCallbackToken = '68xVaE4P6zVD1EjMGwmcQsHot2DFH3BZ3yMHeTuxnKEEVy9f';
    protected $clientRegistrant;
    protected $teamRegistrant;
    //
    protected $clientRegistrantInvoice;
    protected $teamRegistrantInvoice;
    //
    protected $settleInvoicePayload;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createApplication();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('Program')->truncate();
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
        
        $firm = new RecordOfFirm('0');
        $client = new RecordOfClient($firm, '0');
        $team = new RecordOfTeam($firm, $client, '0');
        $program = new RecordOfProgram($firm, '0');
        
        $registrantOne = new RecordOfRegistrant($program, '1');
        $registrantTwo = new RecordOfRegistrant($program, '2');
        
        $this->clientRegistrant = new RecordOfClientRegistrant($client, $registrantOne);
        $this->clientRegistrant->registrant->status = RegistrationStatus::SETTLEMENT_REQUIRED;
        
        $this->teamRegistrant = new RecordOfTeamProgramRegistration($team, $registrantTwo);
        $this->teamRegistrant->registrant->status = RegistrationStatus::SETTLEMENT_REQUIRED;

        $invoiceOne = new RecordOfInvoice('1');
        $invoiceTwo = new RecordOfInvoice('2');
        
        $this->clientRegistrantInvoice = new RecordOfRegistrantInvoice($this->clientRegistrant->registrant, $invoiceOne);
        $this->teamRegistrantInvoice = new RecordOfRegistrantInvoice($this->teamRegistrant->registrant, $invoiceTwo);
        
        $this->settleInvoicePayload = [
            'id' => 'xendit-id',
            'external_id' => $this->clientRegistrantInvoice->invoice->id,
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('Program')->truncate();
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
    
    protected function settleClientRegistrantInvoice()
    {
        $this->clientRegistrant->registrant->program->firm->insert($this->connection);
        $this->clientRegistrant->registrant->program->insert($this->connection);
        $this->clientRegistrant->client->insert($this->connection);
        $this->clientRegistrant->insert($this->connection);
        $this->clientRegistrantInvoice->insert($this->connection);
        
        $this->post('/api/xendit/settle-invoice', $this->settleInvoicePayload, ['x-callback-token' => $this->xenditCallbackToken]);
    }
    public function test_settleClientRegistrantInvoice_200()
    {
        $this->settleClientRegistrantInvoice();
        $this->seeStatusCode(200);
        
        $registrantEntry = [
            'id' => $this->clientRegistrant->registrant->id,
            'status' => RegistrationStatus::ACCEPTED,
        ];
        $this->seeInDatabase('Registrant', $registrantEntry);
        
        $invoiceEntry = [
            'id' => $this->clientRegistrantInvoice->invoice->id,
            'settled' => true,
        ];
        $this->seeInDatabase('Invoice', $invoiceEntry);
        
        $participantEntry = [
            'Program_id' => $this->clientRegistrantInvoice->registrant->program->id,
            'active' => true,
        ];
        $this->seeInDatabase('Participant', $participantEntry);
        
        $clientParticipantEntry = [
            'Client_id' => $this->clientRegistrant->client->id,
        ];
        $this->seeInDatabase('ClientParticipant', $clientParticipantEntry);
    }
    public function test_settleInvoice_invalidToken_403()
    {
        $this->xenditCallbackToken = 'invalid token';
        $this->settleClientRegistrantInvoice();
        $this->seeStatusCode(403);
    }
    
    protected function settleTeamRegistrantInvoice()
    {
        $this->teamRegistrant->registrant->program->firm->insert($this->connection);
        $this->teamRegistrant->registrant->program->insert($this->connection);
        $this->teamRegistrant->team->insert($this->connection);
        $this->teamRegistrant->insert($this->connection);
        $this->teamRegistrantInvoice->insert($this->connection);
        
        $this->settleInvoicePayload['external_id'] = $this->teamRegistrantInvoice->invoice->id;
        $this->post('/api/xendit/settle-invoice', $this->settleInvoicePayload, ['x-callback-token' => $this->xenditCallbackToken]);
    }
    public function test_settleTeamRegistrantInvoice_200()
    {
        $this->settleTeamRegistrantInvoice();
        $this->seeStatusCode(200);
        
        $registrantEntry = [
            'id' => $this->teamRegistrant->registrant->id,
            'status' => RegistrationStatus::ACCEPTED,
        ];
        $this->seeInDatabase('Registrant', $registrantEntry);
        
        $invoiceEntry = [
            'id' => $this->teamRegistrantInvoice->invoice->id,
            'settled' => true,
        ];
        $this->seeInDatabase('Invoice', $invoiceEntry);
        
        $participantEntry = [
            'Program_id' => $this->teamRegistrantInvoice->registrant->program->id,
            'active' => true,
        ];
        $this->seeInDatabase('Participant', $participantEntry);
        
        $teamParticipantEntry = [
            'Team_id' => $this->teamRegistrant->team->id,
        ];
        $this->seeInDatabase('TeamParticipant', $teamParticipantEntry);
    }
    
}
