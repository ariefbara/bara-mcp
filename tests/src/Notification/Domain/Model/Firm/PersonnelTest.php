<?php

namespace Notification\Domain\Model\Firm;

use Notification\Domain\ {
    Model\Firm,
    Model\Firm\Personnel\PersonnelMail,
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
    protected $mailMessage, $modifiedGreetings, $modifiedUrl;
    
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
        $this->modifiedGreetings = $this->buildMockOfClass(MailMessage::class);
        $this->modifiedUrl = $this->buildMockOfClass(MailMessage::class);
    }
    
    public function test_getFullName_returnNamesGetFullNameResult()
    {
        $this->name->expects($this->once())
                ->method("getFullName");
        $this->personnel->getFullName();
    }
    
    protected function executeRegisterAsMailRecipient()
    {
        $this->mailMessage->expects($this->any())
                ->method("appendRecipientFirstNameInGreetings")
                ->willReturn($this->modifiedGreetings);
        $this->modifiedGreetings->expects($this->any())
                ->method("prependUrlPath")
                ->willReturn($this->modifiedUrl);
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
    public function test_registerAsMailRecipient_prependPersonnelUrl()
    {
        $this->modifiedGreetings->expects($this->once())
                ->method("prependUrlPath")
                ->with("/personnel");
        $this->executeRegisterAsMailRecipient();
    }
    public function test_registerAsMailRecipient_addMailToMailGenerator()
    {
        $this->name->expects($this->once())
                ->method("getFullName")
                ->willReturn($fullName = "full name");
        
        $this->mailGenerator->expects($this->once())
                ->method("addMail")
                ->with($this->identicalTo($this->modifiedUrl), $this->personnel->email, $fullName);
        
        $this->executeRegisterAsMailRecipient();
    }
    
    public function test_createResePasswordMail_returnPersonnelMail()
    {
        $this->assertInstanceOf(PersonnelMail::class, $this->personnel->createResetPasswordMail("personnelMailId"));
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
