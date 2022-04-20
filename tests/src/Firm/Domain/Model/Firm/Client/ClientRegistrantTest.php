<?php

namespace Firm\Domain\Model\Firm\Client;

use Firm\Domain\Model\Firm\Client;
use Firm\Domain\Model\Firm\Program\Registrant;
use SharedContext\Domain\Task\Dependency\PaymentGateway;
use SharedContext\Domain\ValueObject\CustomerInfo;
use Tests\TestBase;

class ClientRegistrantTest extends TestBase
{
    protected $clientRegistrant;
    protected $client;
    protected $registrant;
    //
    protected $paymentGateway;
    protected $clientCustomerInfo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientRegistrant = new TestableClientRegistrant();
        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientRegistrant->client = $this->client;
        $this->registrant = $this->buildMockOfClass(Registrant::class);
        $this->clientRegistrant->registrant = $this->registrant;
        //
        $this->paymentGateway = $this->buildMockOfInterface(PaymentGateway::class);
        $this->clientCustomerInfo = $this->buildMockOfClass(CustomerInfo::class);
    }
    
    protected function generateInvoice()
    {
        $this->client->expects($this->any())
                ->method('getClientCustomerInfo')
                ->willReturn($this->clientCustomerInfo);
        $this->clientRegistrant->generateInvoice($this->paymentGateway);
    }
    public function test_generateInvoice_generateRegistrantInvoice()
    {
        $this->registrant->expects($this->once())
                ->method('generateInvoice')
                ->with($this->paymentGateway, $this->clientCustomerInfo);
        $this->generateInvoice();
    }
    
    //
    protected function settleInvoicePayment()
    {
        $this->clientRegistrant->settleInvoicePayment();
    }
    public function test_settleInvoicePayment_settleRegistrantInvoicePayment()
    {
        $this->registrant->expects($this->once())
                ->method('settleInvoicePayment')
                ->with($this->client);
        $this->settleInvoicePayment();
    }
}

class TestableClientRegistrant extends ClientRegistrant
{
    public $client;
    public $id;
    public $registrant;
    
    function __construct()
    {
        parent::__construct();
    }
}
