<?php

namespace Payment\Domain\Task\PaymentGatewayAccount;

use Payment\Domain\Model\PaymentGatewayAccount;
use Resources\Application\Event\AdvanceDispatcher;
use Tests\TestBase;

class SettleInvoiceTest extends TestBase
{
    protected $dispatcher;
    protected $task;
    
    protected $paymentGatewayAccount;
    protected $invoiceId = 'invoiceId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->dispatcher = $this->buildMockOfClass(AdvanceDispatcher::class);
        $this->task = new SettleInvoice($this->dispatcher);
        
        $this->paymentGatewayAccount = $this->buildMockOfClass(PaymentGatewayAccount::class);
    }
    
    protected function execute()
    {
        $this->task->execute($this->paymentGatewayAccount, $this->invoiceId);
    }
    public function test_execute_paymentGatewaySettleInvoice()
    {
        $this->paymentGatewayAccount->expects($this->once())
                ->method('settleInvoice')
                ->with($this->invoiceId);
        $this->execute();
    }
    public function test_execute_dispatchPaymentGatewayAccount()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->paymentGatewayAccount);
        $this->execute();
    }
}
