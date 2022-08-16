<?php

namespace Tests\Controllers;

use SharedContext\Domain\ValueObject\RegistrationStatus;
use Tests\Controllers\ControllerTestCase;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientRegistrant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfRegistrant;
use Tests\Controllers\RecordPreparation\Firm\Program\Registrant\RecordOfRegistrantInvoice;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\RecordOfFirm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfInvoice;

class XenditControllerTest extends ControllerTestCase
{
    protected $xenditCallbackToken = '68xVaE4P6zVD1EjMGwmcQsHot2DFH3BZ3yMHeTuxnKEEVy9f';
    protected $clientRegistrant;
    protected $registrantInvoice;
    //
    protected $settleInvoicePayload;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createApplication();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Registrant')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientRegistrant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('Invoice')->truncate();
        $this->connection->table('RegistrantInvoice')->truncate();
        
        $firm = new RecordOfFirm('0');
        $client = new RecordOfClient($firm, '0');
        $program = new RecordOfProgram($firm, '0');
        $registrant = new RecordOfRegistrant($program, '0');
        $this->clientRegistrant = new RecordOfClientRegistrant($client, $registrant);
        $this->clientRegistrant->registrant->status = RegistrationStatus::SETTLEMENT_REQUIRED;

        $invoice = new RecordOfInvoice('0');
        $this->registrantInvoice = new RecordOfRegistrantInvoice($this->clientRegistrant->registrant, $invoice);
        
        $this->settleInvoicePayload = [
            'id' => 'xendit-id',
            'external_id' => $this->registrantInvoice->invoice->id,
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Registrant')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientRegistrant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('Invoice')->truncate();
        $this->connection->table('RegistrantInvoice')->truncate();
    }
    
    protected function settleInvoice()
    {
        $this->clientRegistrant->registrant->program->firm->insert($this->connection);
        $this->clientRegistrant->registrant->program->insert($this->connection);
        $this->clientRegistrant->client->insert($this->connection);
        $this->clientRegistrant->insert($this->connection);
        $this->registrantInvoice->insert($this->connection);
        
        $this->post('/api/xendit/settle-invoice', $this->settleInvoicePayload, ['x-callback-token' => $this->xenditCallbackToken]);
    }
    public function test_settleInvoice_200()
    {
        $this->settleInvoice();
        $this->seeStatusCode(200);
        
        $registrantEntry = [
            'id' => $this->clientRegistrant->registrant->id,
            'status' => RegistrationStatus::ACCEPTED,
        ];
        $this->seeInDatabase('Registrant', $registrantEntry);
        
        $invoiceEntry = [
            'id' => $this->registrantInvoice->invoice->id,
            'settled' => true,
        ];
        $this->seeInDatabase('Invoice', $invoiceEntry);
        
        $participantEntry = [
            'Program_id' => $this->registrantInvoice->registrant->program->id,
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
        $this->settleInvoice();
        $this->seeStatusCode(403);
    }

}
