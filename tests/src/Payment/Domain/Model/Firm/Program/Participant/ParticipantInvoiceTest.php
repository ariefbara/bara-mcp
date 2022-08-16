<?php

namespace Payment\Domain\Model\Firm\Program\Participant;

use Payment\Domain\Model\Firm\Program\Participant;
use SharedContext\Domain\Model\Invoice;
use Tests\TestBase;

class ParticipantInvoiceTest extends TestBase
{
    protected $participant;
    protected $invoice;
    protected $participantInvoice;
    protected $id = 'newId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->invoice = $this->buildMockOfClass(Invoice::class);
        
        $this->participantInvoice = new TestableParticipantInvoice($this->participant, 'id', $this->invoice);
    }

    protected function construct()
    {
        return new TestableParticipantInvoice($this->participant, $this->id, $this->invoice);
    }
    public function test_construct_setProperties()
    {
        $participantInvoice = $this->construct();
        $this->assertSame($this->participant, $participantInvoice->participant);
        $this->assertSame($this->id, $participantInvoice->id);
        $this->assertSame($this->invoice, $participantInvoice->invoice);
    }
    
    protected function settle()
    {
        $this->participantInvoice->settle();
    }
    public function test_settle_settleInvoice()
    {
        $this->invoice->expects($this->once())
                ->method('settle');
        $this->settle();
    }
    public function test_settle_notifyParticipantSettlementCompleted()
    {
        $this->participant->expects($this->once())
                ->method('settlementCompleted');
        $this->settle();
    }

}

class TestableParticipantInvoice extends ParticipantInvoice
{

    public $participant;
    public $id;
    public $invoice;

}
