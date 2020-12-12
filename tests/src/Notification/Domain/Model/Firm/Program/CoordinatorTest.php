<?php

namespace Notification\Domain\Model\Firm\Program;

use Notification\Domain\Model\Firm\Personnel;
use Notification\Domain\SharedModel\CanSendPersonalizeMail;
use Notification\Domain\SharedModel\ContainNotificationForAllUser;
use SharedContext\Domain\ValueObject\MailMessage;
use Tests\TestBase;

class CoordinatorTest extends TestBase
{
    protected $coordinator;
    protected $personnel;
    protected $mailGenerator, $mailMessage, $modifiedMailMessage, $haltPrependUrlPath = false;
    protected $notification;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->coordinator = new TestableCoordinator();
        $this->personnel = $this->buildMockOfClass(Personnel::class);
        $this->coordinator->personnel = $this->personnel;
        
        $this->mailGenerator = $this->buildMockOfInterface(CanSendPersonalizeMail::class);
        $this->notification = $this->buildMockOfInterface(ContainNotificationForAllUser::class);
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
        $this->modifiedMailMessage = $this->buildMockOfClass(MailMessage::class);
    }
    
    protected function executeRegisterAsMailRecipient()
    {
        $this->mailMessage->expects($this->any())
                ->method("prependUrlPath")
                ->willReturn($this->modifiedMailMessage);
        $this->coordinator->registerAsMailRecipient($this->mailGenerator, $this->mailMessage, $this->haltPrependUrlPath);
    }
    public function test_registerAsMailRecipient_prependCoordinatorUriToMessage()
    {
        $this->mailMessage->expects($this->once())
                ->method("prependUrlPath")
                ->with("/coordinators/{$this->coordinator->id}");
        $this->executeRegisterAsMailRecipient();
    }
    public function test_registerAsMailRecipient_registerPersonnelAsMailRecipient()
    {
        $this->personnel->expects($this->once())
                ->method("registerAsMailRecipient")
                ->with($this->mailGenerator, $this->identicalTo($this->modifiedMailMessage));
        $this->executeRegisterAsMailRecipient();
    }
    public function test_registerAsMailRecipient_haltPrependUrlPaht_preventModifyMailMessageWithUrlPrepend()
    {
        $this->mailMessage->expects($this->never())
                ->method("prependUrlPath");
        $this->haltPrependUrlPath = true;
        $this->executeRegisterAsMailRecipient();
    }
    
    public function test_registerAsNotificationRecipient_addPersonnelAsNotificationRecipient()
    {
        $this->notification->expects($this->once())
                ->method("addPersonnelRecipient")
                ->with($this->personnel);
        $this->coordinator->registerAsNotificationRecipient($this->notification);
    }
}

class TestableCoordinator extends Coordinator
{
    public $program;
    public $id;
    public $personnel;
    
    function __construct()
    {
        parent::__construct();
    }
}
