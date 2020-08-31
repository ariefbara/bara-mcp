<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\Model\Firm;
use Resources\ {
    Application\Service\Mailer,
    DateTimeImmutableBuilder,
    Domain\Model\Mail\Recipient,
    Domain\ValueObject\PersonName
};
use Tests\TestBase;

class ClientTest extends TestBase
{
    protected $client;
    
    protected $firm;
    protected $id = 'newClientId', $firstName = 'hadi', $lastName = 'pranoto', $email = 'covid@hadipranoto.com', 
            $password = 'obatcovid19';
    
    protected $mailer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->firm = $this->buildMockOfClass(Firm::class);
        
        $clientData = new ClientData('hadi', 'pranoto', 'covid@hadipranoto.com', 'obatcovid19');
        $this->client = new TestableClient($this->firm, 'id', $clientData);
        
        $this->mailer = $this->buildMockOfInterface(Mailer::class);
    }
    
    protected function getClientData()
    {
        return new ClientData($this->firstName, $this->lastName, $this->email, $this->password);
    }
    protected function executeConstruct()
    {
        return new TestableClient($this->firm, $this->id, $this->getClientData());
    }
    public function test_construct_setProperties()
    {
        $client = $this->executeConstruct();
        $this->assertEquals($this->firm, $client->firm);
        $this->assertEquals($this->id, $client->id);
        $this->assertEquals($this->email, $client->email);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $client->signupTime);
        $this->assertFalse($client->activated);
        
        $personName = new PersonName($this->firstName, $this->lastName);
        $this->assertEquals($personName, $client->personName);
        
        $this->assertTrue($client->password->match($this->password));
        
        $this->assertNotEmpty($client->activationCode);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy('+24 hours'), $client->activationCodeExpiredTime);
        
        $this->assertNull($client->resetPasswordCode);
        $this->assertNull($client->resetPasswordCodeExpiredTime);
    }
    public function test_construct_invalidEmail_badRequest()
    {
        $this->email = 'invalid format';
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = 'bad request: invalid email format';
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }
    
    public function test_sendActivationCodeMail_executeMailersSendMethod()
    {
        $this->mailer->expects($this->once())
                ->method('send');
        $this->client->sendActivationCodeMail($this->mailer);
    }
    
    public function test_sendResetPasswordCodeMail_sendMailToMailer()
    {
        $this->mailer->expects($this->once())
                ->method('send');
        $this->client->sendResetPasswordCodeMail($this->mailer);
    }
    
    public function test_getMailRecipient_returnRecipient()
    {
        $recipient = new Recipient($this->client->email, $this->client->personName);
        $this->assertEquals($recipient, $this->client->getMailRecipient());
    }
    
    public function test_getName_returnFullName()
    {
        $this->assertEquals($this->client->personName->getFullName(), $this->client->getName());
    }
}

class TestableClient extends Client
{
    public $firm;
    public $id;
    public $personName;
    public $email;
    public $password;
    public $signupTime;
    public $activationCode = null;
    public $activationCodeExpiredTime = null;
    public $resetPasswordCode = null;
    public $resetPasswordCodeExpiredTime = null;
    public $activated = false;

}
