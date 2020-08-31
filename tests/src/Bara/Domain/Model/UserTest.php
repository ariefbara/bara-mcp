<?php

namespace Bara\Domain\Model;

use Resources\ {
    Application\Service\Mailer,
    Domain\ValueObject\PersonName
};
use Tests\TestBase;

class UserTest extends TestBase
{
    protected $user;
    protected $mailer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = new TestableUser();
        $this->user->personName = new PersonName('hadi', 'pranoto');
        $this->user->email = 'user@email.org';
        $this->user->activationCode = bin2hex(random_bytes(32));
        $this->user->resetPasswordCode = bin2hex(random_bytes(32));
        $this->user->activated = false;
        
        $this->mailer = $this->buildMockOfInterface(Mailer::class);
    }
    
    protected function executeSendActivationCodeMail()
    {
        $this->user->sendActivationCodeMail($this->mailer);
    }
    public function test_sendActivationCodeMail_sendMailToMailer()
    {
        $this->mailer->expects($this->once())
                ->method('send');
        $this->executeSendActivationCodeMail();
    }
    public function test_sendActivationCodeMail_emptyActivationCode_forbiddenError()
    {
        $this->user->activationCode = null;
        $operation = function (){
            $this->executeSendActivationCodeMail();
        };
        $errorDetail = 'forbidden: unable to send empty activation code mail';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_sendActivationCodeMail_alreadyActivated_forbiddenError()
    {
        $this->user->activated = true;
        $operation = function (){
            $this->executeSendActivationCodeMail();
        };
        $errorDetail = 'forbidden: unable to send activation code mail for active accout';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    
    protected function executeSendResetPasswordCodeMail()
    {
        $this->user->activated = true;
        $this->user->sendResetPasswordCodeMail($this->mailer);
    }
    public function test_sendResetPasswordCode_sendMailToMailer()
    {
        $this->mailer->expects($this->once())
                ->method('send');
        $this->executeSendResetPasswordCodeMail();
    }
    public function test_sendResetPasswordCode_emptyResetPasswordCode_forbiddenError()
    {
        $this->user->resetPasswordCode = null;
        $operation = function (){
            $this->executeSendResetPasswordCodeMail();
        };
        $errorDetail = 'forbidden: unable to send empty reset password code mail';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_sendResetPasswordCode_inactiveAccount_forbiddenError()
    {
        $this->user->activated = false;
        $operation = function (){
            $this->user->sendResetPasswordCodeMail($this->mailer);
        };
        $errorDetail = 'forbidden: unable to send reset password code mail on inactive Account';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
}

class TestableUser extends User
{
    public $id;
    public $personName;
    public $email;
    public $activationCode;
    public $resetPasswordCode;
    public $activated;
    
    public function __construct()
    {
        parent::__construct();
    }
}
