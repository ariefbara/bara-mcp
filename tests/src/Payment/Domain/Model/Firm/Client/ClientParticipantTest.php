<?php

namespace Payment\Domain\Model\Firm\Client;

use Payment\Domain\Model\Firm\Client;
use Payment\Domain\Model\Firm\Program\Participant;
use SharedContext\Domain\Task\Dependency\PaymentGateway;
use SharedContext\Domain\ValueObject\CustomerInfo;
use Tests\TestBase;

class ClientParticipantTest extends TestBase
{
    protected $clientParticipant, $client, $participant;
    //
    protected $paymentGateway;
    protected $customerInfo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientParticipant = new TestableClientParticipant();
        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientParticipant->client = $this->client;
        
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->clientParticipant->participant = $this->participant;
        
        $this->paymentGateway = $this->buildMockOfInterface(PaymentGateway::class);
        
        $this->customerInfo = $this->buildMockOfClass(CustomerInfo::class);
        $this->client->expects($this->any())
                ->method('generateCustomerInfo')
                ->willReturn($this->customerInfo);
    }
    
    protected function generateInvoice()
    {
        $this->clientParticipant->generateInvoice($this->paymentGateway);
    }
    public function test_generateInvoice_generateParticipantInvoice()
    {
        $this->participant->expects($this->once())
                ->method('generateInvoice')
                ->with($this->paymentGateway, $this->customerInfo);
        $this->generateInvoice();
    }

}

class TestableClientParticipant extends ClientParticipant
{
    public $client;
    public $id = 'clientParticipantId';
    public $participant;
    
    function __construct()
    {
        parent::__construct();
    }
}
