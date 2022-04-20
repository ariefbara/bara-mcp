<?php

namespace Firm\Domain\Task\Responsive;

use Firm\Domain\Model\Firm\Client\ClientRegistrant;
use Firm\Domain\Task\Dependency\Firm\Client\ClientRegistrantRepository;
use Tests\TestBase;

class SettleClientRegistrantInvoicePaymentTest extends TestBase
{
    protected $clientRegistrantRepository, $clientRegistrant, $invoiceId = 'invoiceId';
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->clientRegistrant = $this->buildMockOfClass(ClientRegistrant::class);
        $this->clientRegistrantRepository = $this->buildMockOfInterface(ClientRegistrantRepository::class);
        $this->clientRegistrantRepository->expects($this->any())
                ->method('aClientRegistrantCorrespondWithInvoiceId')
                ->with($this->invoiceId)
                ->willReturn($this->clientRegistrant);
        
        $this->task = new SettleClientRegistrantInvoicePayment($this->clientRegistrantRepository);
    }
    
    protected function execute()
    {
        $this->task->execute($this->invoiceId);
    }
    public function test_execute_settleClientRegistrantInvoicePayment()
    {
        $this->clientRegistrant->expects($this->once())
                ->method('settleInvoicePayment');
        $this->execute();
    }
}
