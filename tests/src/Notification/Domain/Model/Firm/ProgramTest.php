<?php

namespace Notification\Domain\Model\Firm;

use Doctrine\Common\Collections\ArrayCollection;
use Notification\Domain\Model\Firm;
use Notification\Domain\Model\Firm\Program\Coordinator;
use Notification\Domain\SharedModel\CanSendPersonalizeMail;
use Notification\Domain\SharedModel\ContainNotificationforCoordinator;
use SharedContext\Domain\ValueObject\MailMessage;
use Tests\TestBase;

class ProgramTest extends TestBase
{
    protected $program;
    protected $firm;
    protected $coordinator;
    
    protected $mailGenerator, $mailMessage;
    protected $notification;

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = new TestableProgram();
        
        $this->firm = $this->buildMockOfClass(Firm::class);
        $this->program->firm = $this->firm;
        
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->program->coordinators = new ArrayCollection();
        $this->program->coordinators->add($this->coordinator);
        
        $this->mailGenerator = $this->buildMockOfInterface(CanSendPersonalizeMail::class);
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
        $this->notification = $this->buildMockOfInterface(ContainNotificationforCoordinator::class);
    }
    
    public function test_getFirmDomain_returnFirmGetDomainResult()
    {
        $this->firm->expects($this->once())
                ->method("getDomain");
        $this->program->getFirmDomain();
    }
    public function test_getFirmMailSenderAddress_returnFirmsGetMailSenderAddressResult()
    {
        $this->firm->expects($this->once())
                ->method("getMailSenderAddress");
        $this->program->getFirmMailSenderAddress();
    }
    public function test_getFirmMailSenderName_returnFirmsGetMailSenderNameResult()
    {
        $this->firm->expects($this->once())
                ->method("getMailSenderName");
        $this->program->getFirmMailSenderName();
    }
    
    public function test_getFirmLogoPath_returnFirmGetLogoPathResult()
    {
        $this->firm->expects($this->once())
                ->method("getLogoPath");
        $this->program->getFirmLogoPath();
    }
    
    protected function executeRegisterAllCoordinatorsAsMailRecipient()
    {
        $this->coordinator->expects($this->any())
                ->method("isActive")
                ->willReturn(true);
        $this->program->registerAllCoordinatorsAsMailRecipient($this->mailGenerator, $this->mailMessage);
    }
    public function test_registerAllCoordinatorsAsMailRecipient_registerAllCoordinatorsAsMailRecipientWithModifiedMailMessage()
    {
        $modifiedMailMessage = $this->buildMockOfClass(MailMessage::class);
        $this->mailMessage->expects($this->once())
                ->method("PrependUrlPath")
                ->with("/program/{$this->program->id}")
                ->willReturn($modifiedMailMessage);
        $this->coordinator->expects($this->once())
                ->method("registerAsMailRecipient")
                ->with($this->mailGenerator, $this->identicalTo($modifiedMailMessage), $prependUrlPath = false);
        $this->executeRegisterAllCoordinatorsAsMailRecipient();
    }
    public function test_registerAllCoordinatorAsMailRecipient_ignoreSendNotificationToInactiveCoordinator()
    {
        $this->coordinator->expects($this->once())
                ->method("isActive")
                ->willReturn(false);
        $this->coordinator->expects($this->never())
                ->method("registerAsMailRecipient");
        $this->executeRegisterAllCoordinatorsAsMailRecipient();
    }
    
    protected function executeRegisterAllCoordiantorsAsNotificationRecipient()
    {
        $this->coordinator->expects($this->any())
                ->method("isActive")
                ->willReturn(true);
        $this->program->registerAllCoordiantorsAsNotificationRecipient($this->notification);
    }
    public function test_registerAllCoordiantorsAsNotificationRecipient_addAllCoordinatorsAsNotificationRecipient()
    {
        $this->notification->expects($this->once())
                ->method("addCoordinatorAsRecipient")
                ->with($this->coordinator);
        $this->executeRegisterAllCoordiantorsAsNotificationRecipient();
    }
    public function test_registerAllCoordiantorsAsNotificationRecipient_containInactiveCoordinator_prepentSendingNotificationToInactiveCoordiantor()
    {
        $this->coordinator->expects($this->once())
                ->method("isActive")
                ->willReturn(false);
        $this->notification->expects($this->never())
                ->method("addCoordinatorAsRecipient")
                ->with($this->coordinator);
        $this->executeRegisterAllCoordiantorsAsNotificationRecipient();
    }
}

class TestableProgram extends Program
{
    public $firm;
    public $id = "programId";
    public $coordinators;
    
    function __construct()
    {
        parent::__construct();
    }
}
