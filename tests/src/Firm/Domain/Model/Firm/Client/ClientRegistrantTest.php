<?php

namespace Firm\Domain\Model\Firm\Client;

use Firm\Domain\Model\Firm\Client;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\ClientParticipant;
use Firm\Domain\Model\Firm\Program\Participant;
use Firm\Domain\Model\Firm\Program\Registrant;
use SharedContext\Domain\Task\Dependency\PaymentGateway;
use SharedContext\Domain\ValueObject\CustomerInfo;
use Tests\TestBase;

class ClientRegistrantTest extends TestBase
{
    protected $client;
    protected $registrant;
    protected $clientRegistrant;
    //
    protected $id = 'newId';
    //
    protected $paymentGateway;
    protected $clientCustomerInfo;
    //
    protected $program;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->buildMockOfClass(Client::class);
        $this->registrant = $this->buildMockOfClass(Registrant::class);
        $this->clientRegistrant = new TestableClientRegistrant($this->client, 'id', $this->registrant);
        //
        $this->paymentGateway = $this->buildMockOfInterface(PaymentGateway::class);
        $this->clientCustomerInfo = $this->buildMockOfClass(CustomerInfo::class);
        //
        $this->program = $this->buildMockOfClass(Program::class);
    }
    
    //
    protected function construct()
    {
        return new TestableClientRegistrant($this->client, $this->id, $this->registrant);
    }
    public function test_construct_setProperties()
    {
        $clientRegistrant = $this->construct();
        $this->assertSame($this->client, $clientRegistrant->client);
        $this->assertSame($this->id, $clientRegistrant->id);
        $this->assertSame($this->registrant, $clientRegistrant->registrant);
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
    
    //
    protected function isUnconcludedRegistrationInProgram()
    {
        return $this->clientRegistrant->isUnconcludedRegistrationInProgram($this->program);
    }
    public function test_isUnconcludedRegistrationInProgram_returnRegistrantEvaluationResult()
    {
        $this->registrant->expects($this->once())
                ->method('isUnconcludedRegistrationInProgram')
                ->with($this->program);
        $this->isUnconcludedRegistrationInProgram();
    }
    
    //
    protected function addAsProgramParticipant()
    {
        $this->clientRegistrant->addAsProgramParticipant();
    }
    public function test_addAsProgramParticipant_addClientAsProgramParticipantInRegistrant()
    {
        $this->registrant->expects($this->once())
                ->method('addApplicantAsParticipant')
                ->with($this->client);
        $this->addAsProgramParticipant();
    }
}

class TestableClientRegistrant extends ClientRegistrant
{
    public $client;
    public $id;
    public $registrant;
    
}
