<?php

namespace Notification\Domain\Model\Firm\Program;

use Notification\Domain\Model\Firm\Program;
use Notification\Domain\SharedModel\CanSendPersonalizeMail;
use Notification\Domain\SharedModel\ContainNotificationforCoordinator;
use SharedContext\Domain\ValueObject\MailMessage;
use Tests\TestBase;

class ConsultationSetupTest extends TestBase
{
    protected $consultationSetup;
    protected $program;
    protected $mailGenerator, $mailMessage;
    protected $notification;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSetup = new TestableConsultationSetup();
        
        $this->program = $this->buildMockOfClass(Program::class);
        $this->consultationSetup->program = $this->program;
        
        $this->mailGenerator = $this->buildMockOfInterface(CanSendPersonalizeMail::class);
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
        
        $this->notification = $this->buildMockOfInterface(ContainNotificationforCoordinator::class);
    }
    
    public function test_registerAllCoordinatorsAsMailRecipient_registerAllProgramsCoordinatorsAsMailRecipient()
    {
        $this->program->expects($this->once())
                ->method("registerAllCoordinatorsAsMailRecipient")
                ->with($this->mailGenerator, $this->mailMessage);
        $this->consultationSetup->registerAllCoordinatorsAsMailRecipient($this->mailGenerator, $this->mailMessage);
    }
    
    public function test_registerAllCoordinatorsAsNotificationRecipient_registerAllProgramsCoordinatorAsNotificationRecipient()
    {
        $this->program->expects($this->once())
                ->method("registerAllCoordiantorsAsNotificationRecipient")
                ->with($this->notification);
        $this->consultationSetup->registerAllCoordinatorsAsNotificationRecipient($this->notification);
    }
}

class TestableConsultationSetup extends ConsultationSetup
{
    public $program;
    public $id;
    
    function __construct()
    {
        parent::__construct();
    }
}
