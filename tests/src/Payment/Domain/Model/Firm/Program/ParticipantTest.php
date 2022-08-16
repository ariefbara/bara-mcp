<?php

namespace Payment\Domain\Model\Firm\Program;

use Payment\Domain\Model\Firm\Program;
use Payment\Domain\Model\Firm\Program\Participant\ParticipantInvoice;
use SharedContext\Domain\Model\Invoice;
use SharedContext\Domain\Task\Dependency\InvoiceParameter;
use SharedContext\Domain\Task\Dependency\PaymentGateway;
use SharedContext\Domain\ValueObject\CustomerInfo;
use SharedContext\Domain\ValueObject\ItemInfo;
use SharedContext\Domain\ValueObject\ParticipantStatus;
use Tests\TestBase;

class ParticipantTest extends TestBase
{
    protected $participant, $program, $programName = 'programName', $status;
    //
    protected $paymentGateway, $customerInfo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = new TestableParticipant();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->program->expects($this->any())->method('getName')->willReturn($this->programName);
        $this->participant->program = $this->program;
        $this->status = $this->buildMockOfClass(ParticipantStatus::class);
        $this->participant->status = $this->status;
        //
        $this->paymentGateway = $this->buildMockOfClass(PaymentGateway::class);
        $this->customerInfo = $this->buildMockOfClass(CustomerInfo::class);
    }
    
    protected function generateInvoice()
    {
        $this->participant->generateInvoice($this->paymentGateway, $this->customerInfo);
    }
    public function test_generateInvoice_setParticipantInvoice()
    {
        $id = $this->participant->id;
        $amount = $this->participant->programPrice;
        $description = "invoice pendaftaran program: {$this->programName}";
        $duration = 7*24*60*60;
        $itemInfo = new ItemInfo($this->programName, 1, $this->participant->programPrice, null, null);
        
        $invoiceParamenter = new InvoiceParameter($id, $amount, $description, $duration, $this->customerInfo, $itemInfo);
        $invoice = $this->buildMockOfClass(Invoice::class);
        $this->paymentGateway->expects($this->any())
                ->method('generateInvoice')
                ->with($this->participant->id, $invoiceParamenter)
                ->willReturn($invoice);
        
        $this->generateInvoice();
        
        $participantInvoice = new ParticipantInvoice($this->participant, $this->participant->id, $invoice);
        $this->assertEquals($participantInvoice, $this->participant->participantInvoice);
    }
    public function test_generateInvoice_alreadyHasInvoice_forbidden()
    {
        $participantInvoice = $this->buildMockOfClass(ParticipantInvoice::class);
        $this->participant->participantInvoice = $participantInvoice;
        $this->assertRegularExceptionThrowed(function () {
            $this->generateInvoice();
        }, 'Forbidden', 'item can only has one invoice');
    }
    
    protected function settlementCompleted()
    {
        $this->participant->settlementCompleted();
    }
    public function test_settlementCompleted_updateStatusPaymentSettle()
    {
        $this->status->expects($this->once())
                ->method('settlePayment')
                ->willReturn($status = $this->buildMockOfClass(ParticipantStatus::class));
        $this->settlementCompleted();
        $this->assertSame($status, $this->participant->status);
    }
}

class TestableParticipant extends Participant
{
    public $program;
    public $id = 'participantId';
    public $status;
    public $programPrice = 75000;
    public $participantInvoice;
    
    function __construct()
    {
        parent::__construct();
    }
}
