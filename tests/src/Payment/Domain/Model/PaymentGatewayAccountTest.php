<?php

namespace Payment\Domain\Model;

use Config\EventList;
use Payment\Domain\Task\PaymentGatewayAccount\PaymentGatewayAccountTask;
use Resources\Domain\Event\CommonEvent;
use Tests\TestBase;

class PaymentGatewayAccountTest extends TestBase
{
    protected $paymentGatewayAccount;
    protected $paymentGatewayAccountTask, $payload = 'string represent task payload';
    //
    protected $invoiceId = 'invoiceId';


    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentGatewayAccount = new TestablePaymentGatewayAccount();
        
        $this->paymentGatewayAccountTask = $this->buildMockOfInterface(PaymentGatewayAccountTask::class);
    }
    
    protected function executePaymentGatewayAccountTask()
    {
        $this->paymentGatewayAccount->executePaymentGatewayAccountTask($this->paymentGatewayAccountTask, $this->payload);
    }
    public function test_executePaymentGatewayAccountTask_executeTask()
    {
        $this->paymentGatewayAccountTask->expects($this->once())
                ->method('execute')
                ->with($this->paymentGatewayAccount, $this->payload);
        $this->executePaymentGatewayAccountTask();
    }
    
    protected function settleInvoice()
    {
        $this->paymentGatewayAccount->settleInvoice($this->invoiceId);
    }
    public function test_settleInvoice_recordEvent()
    {
        $this->settleInvoice();
        
        $event = new CommonEvent(EventList::INVOICE_SETTLED, $this->invoiceId);
        $this->assertEquals($event, $this->paymentGatewayAccount->recordedEvents[0]);
    }
}

class TestablePaymentGatewayAccount extends PaymentGatewayAccount
{
    public $recordedEvents;
}
