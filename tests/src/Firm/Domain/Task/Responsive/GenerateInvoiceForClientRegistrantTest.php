<?php

namespace Firm\Domain\Task\Responsive;

use Firm\Domain\Model\Firm\Client\ClientRegistrant;
use Firm\Domain\Task\Dependency\Firm\Client\ClientRegistrantRepository;
use SharedContext\Domain\Task\Dependency\PaymentGateway;
use Tests\TestBase;

class GenerateInvoiceForClientRegistrantTest extends TestBase
{
    protected $clientRegistrantRepository, $clientRegistrant, $clientRegistrantId = 'client-registrant-id';
    protected $paymentGateway;
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientRegistrant = $this->buildMockOfClass(ClientRegistrant::class);
        $this->clientRegistrantRepository = $this->buildMockOfClass(ClientRegistrantRepository::class);
        $this->clientRegistrantRepository->expects($this->any())
                ->method('ofId')
                ->with($this->clientRegistrantId)
                ->willReturn($this->clientRegistrant);
        
        $this->paymentGateway = $this->buildMockOfInterface(PaymentGateway::class);
        
        $this->task = new GenerateInvoiceForClientRegistrant($this->clientRegistrantRepository, $this->paymentGateway);
    }
    
    protected function execute()
    {
        $this->task->execute($this->clientRegistrantId);
    }
    public function test_execute_generateClientRegistrantInvoice()
    {
        $this->clientRegistrant->expects($this->once())
                ->method('generateInvoice')
                ->with($this->paymentGateway);
        $this->execute();
    }
}
