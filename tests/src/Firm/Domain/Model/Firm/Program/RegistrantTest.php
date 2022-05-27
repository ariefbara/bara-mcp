<?php

namespace Firm\Domain\Model\Firm\Program;

use Config\EventList;
use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\Event\ProgramRegistrationReceived;
use Firm\Domain\Model\Firm\Client;
use Firm\Domain\Model\Firm\IProgramApplicant;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\Registrant\RegistrantInvoice;
use Firm\Domain\Model\Firm\Program\Registrant\RegistrantProfile;
use Firm\Domain\Model\Firm\Team;
use Firm\Domain\Model\User;
use Resources\DateTimeImmutableBuilder;
use Resources\Domain\Event\CommonEvent;
use SharedContext\Domain\Model\Invoice;
use SharedContext\Domain\Task\Dependency\InvoiceParameter;
use SharedContext\Domain\Task\Dependency\PaymentGateway;
use SharedContext\Domain\ValueObject\CustomerInfo;
use SharedContext\Domain\ValueObject\ItemInfo;
use SharedContext\Domain\ValueObject\ProgramSnapshot;
use SharedContext\Domain\ValueObject\RegistrationStatus;
use Tests\TestBase;

class RegistrantTest extends TestBase
{
    protected $program, $programSnapshot;
    protected $applicant;
    protected $registrant;
    //
    protected $registrationStatus;
    protected $profile;
    protected $userRegistrant;
    protected $clientRegistrant;
    protected $teamRegistrant;

    protected $id = 'newRegistrantId';
    
    protected $participantId = 'participantId';
    
    protected $user, $client, $team;
    //
    protected $paymentGateway, $customerInfo;
    protected $programItemInfo, $programSnapshotPrice = 40000, $programDescription = 'program description';
    //
    protected $registrantInvoice;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->program = $this->buildMockOfClass(Program::class);
        $this->programSnapshot = $this->buildMockOfClass(ProgramSnapshot::class);
        $this->applicant = $this->buildMockOfInterface(IProgramApplicant::class);
        $this->registrant = new TestableRegistrant($this->program, $this->programSnapshot, 'id', $this->applicant);
        
        $this->registrationStatus = $this->buildMockOfClass(RegistrationStatus::class);
        $this->registrant->status = $this->registrationStatus;
        
        $this->profile = $this->buildMockOfClass(RegistrantProfile::class);
        $this->registrant->profiles = new ArrayCollection();
        $this->registrant->profiles->add($this->profile);
        
        $this->userRegistrant = $this->buildMockOfClass(UserRegistrant::class);
        $this->registrant->userRegistrant = $this->userRegistrant;
        
        $this->clientRegistrant = $this->buildMockOfClass(ClientRegistrant::class);
        
        $this->teamRegistrant = $this->buildMockOfClass(TeamRegistrant::class);
        
        $this->user = $this->buildMockOfClass(User::class);
        $this->client = $this->buildMockOfClass(Client::class);
        $this->team = $this->buildMockOfClass(Team::class);
        //
        $this->paymentGateway = $this->buildMockOfInterface(PaymentGateway::class);
        $this->customerInfo = $this->buildMockOfClass(CustomerInfo::class);
        $this->programItemInfo = $this->buildMockOfClass(ItemInfo::class);
        //
        $this->registrantInvoice = $this->buildMockOfClass(RegistrantInvoice::class);
    }
    
    //
    protected function construct()
    {
        $this->programSnapshot->expects($this->any())
                ->method('generateInitialRegistrationStatus')
                ->willReturn($this->registrationStatus);
        return new TestableRegistrant($this->program, $this->programSnapshot, $this->id, $this->applicant);
    }
    public function test_construct_setProperties()
    {
        $registrant = $this->construct();
        $this->assertSame($this->program, $registrant->program);
        $this->assertSame($this->programSnapshot, $registrant->programSnapshot);
        $this->assertSame($this->id, $registrant->id);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $registrant->registeredTime);
    }
    public function test_construct_setStatusFromProgramSnapshot()
    {
        $registrant = $this->construct();
        $this->assertSame($this->registrationStatus, $registrant->status);
    }
    public function test_construct_addApplicantProgramRegistration()
    {
        $this->applicant->expects($this->once())
                ->method('addProgramRegistration');
        $this->construct();
    }
    public function test_construct_storeProgramRegistrationReceivedEvent()
    {
        $event = new ProgramRegistrationReceived($this->id, $this->registrationStatus);
        $registrant = $this->construct();
        $this->assertEquals($event, $registrant->recordedEvents[0]);
    }
    
/*
    //
    protected function executeAccept()
    {
        $this->registrant->accept();
    }
    public function test_accept_setConcludedTrueAndNoteAccepted()
    {
        
        $this->executeAccept();
        $status = new RegistrationStatus(RegistrationStatus::ACCEPTED);
        $this->assertEquals($status, $this->registrant->status);
    }
    public function test_accept_alreadyConcluded_forbiddenError()
    {
        $this->registrant->concluded = true;
        $operation = function () {
            $this->executeAccept();
        };
        $errorDetail = "forbidden: application already concluded";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }

    //
    protected function executeReject()
    {
        $this->registrant->reject();
    }
    public function test_reject_setConcludedFlagTrueAndNoteRejected()
    {
        $this->executeReject();
        $this->assertTrue($this->registrant->concluded);
        $this->assertEquals('rejected', $this->registrant->note);
    }
    public function test_reject_alreadyConcluded_throwEx()
    {
        $this->registrant->concluded = true;
        $operation = function () {
            $this->executeReject();
        };
        $errorDetail = "forbidden: application already concluded";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
*/
    
/*
    protected function executeCreateParticipant()
    {
        return $this->registrant->createParticipant($this->participantId);
    }
    public function test_createParticipant_aUserRegistrant_returnUserRegistrantCreateParticipantResult()
    {
        $this->userRegistrant->expects($this->once())
                ->method('createParticipant')
                ->with($this->program, $this->participantId);
        $this->executeCreateParticipant();
    }
    public function test_createParticipant_aClientRegistrant_returnClientRegistrantCreateParticipantResult()
    {
        $this->registrant->userRegistrant = null;
        $this->registrant->clientRegistrant = $this->clientRegistrant;
        
        $this->clientRegistrant->expects($this->once())
                ->method('createParticipant')
                ->with($this->program, $this->participantId);
        $this->executeCreateParticipant();
    }
    public function test_createParticipant_aTeamRegistrant_returnTeamRegistrantCreateParticipantResult()
    {
        $this->registrant->userRegistrant = null;
        $this->registrant->teamRegistrant = $this->teamRegistrant;
        
        $this->teamRegistrant->expects($this->once())
                ->method('createParticipant')
                ->with($this->program, $this->participantId);
        $this->executeCreateParticipant();
    }
    public function test_createParticipant_transferAllProfileToParticipant()
    {
        $this->profile->expects($this->once())
                ->method("transferToParticipant");
        $this->executeCreateParticipant();
    }
    public function test_createParticipant_preventTransferRemovedProfile()
    {
        $this->profile->expects($this->once())
                ->method("isRemoved")
                ->willReturn(true);
        $this->profile->expects($this->never())
                ->method("transferToParticipant");
        $this->executeCreateParticipant();
    }
    
    public function test_correspondWithUser_returnUserRegistrantUserEqualsResult()
    {
        $this->userRegistrant->expects($this->once())
                ->method('userEquals')
                ->with($this->user);
        $this->registrant->correspondWithUser($this->user);
    }
    public function test_correspondWithUser_emptyUserRegistrant_returnFalse()
    {
        $this->registrant->userRegistrant = null;
        $this->assertFalse($this->registrant->correspondWithUser($this->user));
    }
    
    public function test_correspondWithClient_emptyClientRegistrant_returnFalse()
    {
        $this->assertFalse($this->registrant->correspondWithClient($this->client));
    }
    public function test_correspondWithClient_hasClientRegistrant_returnClientRegistrantsClientIdEqualsResult()
    {
        $this->registrant->clientRegistrant = $this->clientRegistrant;
        $this->clientRegistrant->expects($this->once())
                ->method('clientEquals')
                ->with($this->client);
        $this->registrant->correspondWithClient($this->client);
    }
    
    protected function correspondWithTeam()
    {
        return $this->registrant->correspondWithTeam($this->team);
    }
    public function test_correspondWithTeam_emptyTeamRegistrant_returnFalse()
    {
        $this->assertFalse($this->correspondWithTeam());
    }
    public function test_correspondWithTeam_hasTeamRegistrant_returnTeamRegistrantsTeamEqualsResult()
    {
        $this->registrant->teamRegistrant = $this->teamRegistrant;
        $this->teamRegistrant->expects($this->once())
                ->method('teamEquals')
                ->with($this->team)
                ->willReturn(true);
        $this->assertTrue($this->correspondWithTeam());
    }
 * 
 */
    
    //
    protected function generateInvoice()
    {
        $this->programSnapshot->expects($this->any())
                ->method('generateItemInfo')
                ->willReturn($this->programItemInfo);
        $this->programSnapshot->expects($this->once())
                ->method('getPrice')
                ->willReturn($this->programSnapshotPrice);
        $this->registrant->generateInvoice($this->paymentGateway, $this->customerInfo);
    }
    public function test_generateInvoid_setRegistrantInvoice()
    {
        $description = 'tagihan pendaftaran program';
        $invoiceDuration = 7*24*60*60;
        $invoiceParameter = new InvoiceParameter(
                $this->registrant->id, $this->programSnapshotPrice, $description, $invoiceDuration, 
                $this->customerInfo, $this->programItemInfo);
        $this->paymentGateway->expects($this->once())
                ->method('generateInvoiceLink')
                ->with($invoiceParameter)
                ->willReturn($paymentLink = 'random string represent invoice link');
        $invoice = new Invoice(
                $this->registrant->id, DateTimeImmutableBuilder::buildYmdHisAccuracy('+7 days'), $paymentLink);
        $registrantInvoice = new RegistrantInvoice($this->registrant, $this->registrant->id, $invoice);
        $this->generateInvoice();
        
        $this->assertEquals($registrantInvoice, $this->registrant->registrantInvoice);
    }
    
    //
    protected function settleInvoicePayment()
    {
        $this->registrant->registrantInvoice = $this->registrantInvoice;
        $this->registrant->settleInvoicePayment($this->applicant);
    }
    public function test_settleInvoicePayment_updateStatus()
    {
        $this->registrationStatus->expects($this->once())
                ->method('settle')
                ->willReturn($newStatus = $this->buildMockOfClass(RegistrationStatus::class));
        $this->settleInvoicePayment();
        $this->assertSame($newStatus, $this->registrant->status);
    }
    public function test_settleInvoicePayment_settleInvoice()
    {
        $this->registrantInvoice->expects($this->once())
                ->method('settle');
        $this->settleInvoicePayment();
    }
    public function test_settleInvoicePayment_noInvoice_Forbidden()
    {
        $this->registrant->registrantInvoice = null;
        $this->assertRegularExceptionThrowed(function(){
            $this->registrant->settleInvoicePayment($this->applicant);
        }, 'Forbidden', 'no invoice found');
    }
    public function test_settleInvoicePayment_addApplicantAsProgramParticipant()
    {
        $this->program->expects($this->once())
                ->method('addApplicantAsParticipant')
                ->with($this->applicant);
        $this->settleInvoicePayment();
    }

}

class TestableRegistrant extends Registrant
{
    public $program;
    public $programSnapshot;
    public $id;
    public $status;
    public $registeredTime;
    public $userRegistrant;
    public $clientRegistrant;
    public $teamRegistrant;
    public $profiles;
    public $registrantInvoice;
    //
    public $recordedEvents;
    public $aggregatedEventsFromBranches;
}
