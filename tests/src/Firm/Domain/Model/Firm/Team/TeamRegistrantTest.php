<?php

namespace Firm\Domain\Model\Firm\Team;

use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\Registrant;
use Firm\Domain\Model\Firm\Team;
use SharedContext\Domain\Task\Dependency\PaymentGateway;
use SharedContext\Domain\ValueObject\CustomerInfo;
use Tests\TestBase;

class TeamRegistrantTest extends TestBase
{

    protected $team;
    protected $registrant;
    protected $teamRegistrant;
    //
    protected $id = 'newId';
    //
    protected $program;
    //
    protected $paymentGateway;

    protected function setUp(): void
    {
        parent::setUp();
        $this->team = $this->buildMockOfClass(Team::class);
        $this->registrant = $this->buildMockOfClass(Registrant::class);
        $this->teamRegistrant = new TestableTeamRegistrant($this->team, 'id', $this->registrant);
        //
        $this->program = $this->buildMockOfClass(Program::class);
        //
        $this->paymentGateway = $this->buildMockOfInterface(PaymentGateway::class);
        
    }
    
    protected function construct()
    {
        return new TestableTeamRegistrant($this->team, $this->id, $this->registrant);
    }
    public function test_construct_setProperties()
    {
        $teamRegistrant = $this->construct();
        $this->assertSame($this->team, $teamRegistrant->team);
        $this->assertSame($this->id, $teamRegistrant->id);
        $this->assertSame($this->registrant, $teamRegistrant->registrant);
    }
    
    //
    protected function isUnconcludedRegistrationInProgram()
    {
        return $this->teamRegistrant->isUnconcludedRegistrationInProgram($this->program);
    }
    public function test_isUnconcludedRegistrationInProgram_returnRegistrantEvaluationResult()
    {
        $this->registrant->expects($this->once())
                ->method('isUnconcludedRegistrationInProgram')
                ->with($this->program);
        $this->isUnconcludedRegistrationInProgram();
    }
    
    //
    protected function generateInvoice()
    {
        $this->teamRegistrant->generateInvoice($this->paymentGateway);
    }
    public function test_generateInvoice_generateRegistrantInvoice()
    {
        $this->team->expects($this->once())
                ->method('getCustomerInfo')
                ->willReturn($customerInfo = $this->buildMockOfClass(CustomerInfo::class));
        $this->registrant->expects($this->once())
                ->method('generateInvoice')
                ->with($this->paymentGateway, $customerInfo);
        $this->generateInvoice();
    }
    
    //
    protected function settleInvoicePayment()
    {
        $this->teamRegistrant->settleInvoicePayment();
    }
    public function test_settleInvoicePayment_settleRegistrantInvoicePayment()
    {
        $this->registrant->expects($this->once())
                ->method('settleInvoicePayment')
                ->with($this->team);
        $this->settleInvoicePayment();
    }
    
    //
    protected function addAsProgramParticipant()
    {
        $this->teamRegistrant->addAsProgramParticipant();
    }
    public function test_addAsProgramParticipant_addTeamAsProgramParticipant()
    {
        $this->registrant->expects($this->once())
                ->method('addApplicantAsParticipant')
                ->with($this->team);
        $this->addAsProgramParticipant();
    }

}

class TestableTeamRegistrant extends TeamRegistrant
{
    public $team;
    public $id;
    public $registrant;
}
