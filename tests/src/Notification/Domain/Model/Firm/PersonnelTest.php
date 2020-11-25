<?php

namespace Notification\Domain\Model\Firm;

use Notification\Domain\ {
    Model\Firm,
    SharedModel\CanSendPersonalizeMail
};
use Resources\Domain\ValueObject\PersonName;
use SharedContext\Domain\ValueObject\MailMessage;
use Tests\TestBase;

class PersonnelTest extends TestBase
{
    protected $personnel;
    protected $firm;
    protected $name;
    
    protected $mailGenerator;
    protected $mailMessage;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->personnel = new TestablePersonnel();
        $this->personnel->email = 'personnel@email.org';
        
        $this->firm = $this->buildMockOfClass(Firm::class);
        $this->personnel->firm = $this->firm;
        
        $this->name = $this->buildMockOfClass(PersonName::class);
        $this->personnel->name = $this->name;
        
        $this->mailGenerator = $this->buildMockOfInterface(CanSendPersonalizeMail::class);
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
    }
    
    public function test_getFullName_returnNamesGetFullNameResult()
    {
        $this->name->expects($this->once())
                ->method("getFullName");
        $this->personnel->getFullName();
    }
    
    protected function executeRegisterAsMailRecipient()
    {
        $this->personnel->registerAsMailRecipient($this->mailGenerator, $this->mailMessage);
    }
    public function test_registerAsMailRecipient_appendFirstNameInMailMessage()
    {
        $this->name->expects($this->once())
                ->method("getFirstName")
                ->willReturn($firstName = "first name");
        $this->mailMessage->expects($this->once())
                ->method("appendRecipientFirstNameInGreetings")
                ->with($firstName);
        $this->executeRegisterAsMailRecipient();
    }
    public function test_registerAsMailRecipient_addMailToMailGenerator()
    {
        $mailMessage = $this->buildMockOfClass(MailMessage::class);
        $this->mailMessage->expects($this->once())
                ->method("appendRecipientFirstNameInGreetings")
                ->willReturn($mailMessage);
        
        $this->name->expects($this->once())
                ->method("getFullName")
                ->willReturn($fullName = "full name");
        
        $this->mailGenerator->expects($this->once())
                ->method("addMail")
                ->with($mailMessage, $this->personnel->email, $fullName);
        
        $this->executeRegisterAsMailRecipient();
    }
}

class TestablePersonnel extends Personnel
{
    public $firm;
    public $id;
    public $name;
    public $email;
    
    function __construct()
    {
        parent::__construct();
    }
}
