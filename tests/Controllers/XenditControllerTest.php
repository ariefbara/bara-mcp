<?php

namespace Tests\Controllers;

use SharedContext\Domain\ValueObject\ParticipantStatus;
use Tests\Controllers\ControllerTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfParticipantInvoice;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\RecordOfFirm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfInvoice;

class XenditControllerTest extends ControllerTestCase
{
    protected $xenditCallbackToken = '68xVaE4P6zVD1EjMGwmcQsHot2DFH3BZ3yMHeTuxnKEEVy9f';
    protected $participantInvoice;
    //
    protected $settleInvoicePayload;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createApplication();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Invoice')->truncate();
        $this->connection->table('ParticipantInvoice')->truncate();
        
        $firm = new RecordOfFirm('0');
        $program = new RecordOfProgram($firm, '0');
        
        $participant = new RecordOfParticipant($program, '1');
        $participant ->status = ParticipantStatus::SETTLEMENT_REQUIRED;

        $invoice = new RecordOfInvoice('1');
        $this->participantInvoice = new RecordOfParticipantInvoice($participant , $invoice );
        
        $this->settleInvoicePayload = [
            'id' => 'xendit-id',
            'external_id' => $this->participantInvoice->invoice->id,
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Invoice')->truncate();
        $this->connection->table('ParticipantInvoice')->truncate();
    }
    
    protected function settleInvoice()
    {
        $this->participantInvoice->participant->program->firm->insert($this->connection);
        $this->participantInvoice->participant->program->insert($this->connection);
        $this->participantInvoice->participant->insert($this->connection);
        $this->participantInvoice->insert($this->connection);
        
        $this->post('/api/xendit/settle-invoice', $this->settleInvoicePayload, ['x-callback-token' => $this->xenditCallbackToken]);
    }
    public function test_settleInvoice_setInvoiceSettled_200()
    {
$this->disableExceptionHandling();
        $this->settleInvoice();
        $this->seeStatusCode(200);
        
        $invoiceEntry = [
            'id' => $this->participantInvoice->invoice->id,
            'settled' => true
        ];
        $this->seeInDatabase('Invoice', $invoiceEntry);
    }
    public function test_settleInvoice_updateParticipantStatus()
    {
        $this->settleInvoice();
        $this->seeStatusCode(200);
        
        $participantEntry = [
            'id' => $this->participantInvoice->participant->id,
            'status' => ParticipantStatus::ACTIVE,
        ];
        $this->seeInDatabase('Participant', $participantEntry);
    }
    public function test_settleInvoice_invalidToken_403()
    {
        $this->xenditCallbackToken = 'invalid token';
        $this->settleInvoice();
        $this->seeStatusCode(403);
    }

}
