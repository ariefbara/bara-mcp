<?php

namespace Client\Domain\Model;

use Client\Domain\ {
    Event\ClientActivationCodeGenerated,
    Event\ClientPasswordResetCodeGenerated,
    Model\Client\ClientNotification,
    Model\Client\ProgramParticipation,
    Model\Client\ProgramRegistration,
    Model\Firm\Program
};
use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Resources\Domain\ValueObject\Password;
use Shared\Domain\Model\Notification;
use Tests\TestBase;

class ClientTest extends TestBase
{

    protected $client;
    protected $id = 'new-user-id';
    protected $name = 'new talent';
    protected $email = 'new_user@email.org';
    protected $password = 'password123';
    protected $baseEmail = 'base_address@email.org', $basePassword = 'basePwd123';
    
    protected $program;
    protected $programParticipation;
    protected $programRegistration;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = new TestableClient('id', 'base name', $this->baseEmail,
            $this->basePassword);

        $this->client->generateResetPasswordCode();
        
        $this->program = $this->buildMockOfClass(Program::class);
        $this->program->expects($this->any())
                ->method('canAcceptRegistration')
                ->willReturn(true);
        $this->client->programParticipations = new ArrayCollection();
        $this->client->programRegistrations = new ArrayCollection();
        
        $this->programParticipation = $this->buildMockOfClass(ProgramParticipation::class);
        $this->client->programParticipations->add($this->programParticipation);
        $this->programRegistration = $this->buildMockOfClass(ProgramRegistration::class);
        $this->client->programRegistrations->add($this->programRegistration);
    }

    private function executeConstruct()
    {
        return new TestableClient($this->id, $this->name, $this->email, $this->password);
    }

    public function test_construct_setProperties()
    {
        $client = $this->executeConstruct();
        $this->assertEquals($this->id, $client->id);
        $this->assertEquals($this->name, $client->name);
        $this->assertEquals($this->email, $client->email);
        $this->assertTrue($client->password->match($this->password));
        $this->assertEquals($this->YmdHisStringOfCurrentTime(), $client->signupTime->format('Y-m-d H:i:s'));
        $this->assertFalse($client->activated);
    }

    public function test_construct_emptyName_throwEx()
    {
        $this->name = '';
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = "bad request: client name is required";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }

    public function test_construct_invalidEmail_throwEx()
    {
        $this->email = "invalid format";
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = "bad request: client email is required and must be in valid email address format";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }

    function test_construct_setActivationCodeAndExpiredTime()
    {
        $client = $this->executeConstruct();
        $this->assertNotEmpty($client->activationCode);
        $this->assertEquals((new DateTime('+24 hours'))->format("Y-m-d H:i:s"),
            $client->activationCodeExpiredTime->format('Y-m-d H:i:s'));
    }

    function test_construct_addTalentActivationCodeGeneratedEvent()
    {
        $user = $this->executeConstruct();
        $this->assertInstanceOf(ClientActivationCodeGenerated::class, $user->getRecordedEvents()[0]);
    }

    private function executeChangeProfile()
    {
        $this->client->changeProfile($this->name);
    }

    function test_update_changeName()
    {
        $this->executeChangeProfile();
        $this->assertEquals($this->name, $this->client->name);
    }

    public function test_update_emptyName_throwEx()
    {
        $this->name = '';
        $operation = function () {
            $this->executeChangeProfile();
        };
        $errorDetail = "bad request: client name is required";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }

    function test_generateActivationCode_setActivationCodeAndExpiredDateAlsoRecordTalentActivationCodeGeneratedEvent()
    {
        $this->client->activationCode = null;
        $this->client->activationCodeExpiredTime = null;
        $this->client->clearRecordedEvents();
        $this->client->generateActivationCode();
        $this->assertNotEmpty($this->client->activationCode);
        $this->assertEquals((new DateTime('+24 hours'))->format('Y-m-d H:i:s'),
            $this->client->activationCodeExpiredTime->format('Y-m-d H:i:s'));
        $this->assertInstanceOf(ClientActivationCodeGenerated::class,
            $this->client->getRecordedEvents()[0]);
    }

    function test_generateActivationCode_accountAlreadyActivated_throwEx()
    {
        $this->client->activate($this->client->activationCode);
        $operation = function () {
            $this->client->generateActivationCode();
        };
        $errorDetail = 'forbidden: account already activated';
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }

    private function executeGenerateResetPasswordCode()
    {
        $this->client->generateResetPasswordCode();
    }

    function test_generateResetPasswordCode_setResetPasswordCode()
    {
        $this->executeGenerateResetPasswordCode();
        $this->assertNotEmpty($this->client->resetPasswordCode);
    }

    function test_generateResetPasswordCode_setExpiredTime()
    {
        $this->executeGenerateResetPasswordCode();
        $this->assertEquals((new DateTime('+24 hours'))->format('Y-m-d H:i:s'),
            $this->client->resetPasswordCodeExpiredTime->format('Y-m-d H:i:s'));
    }

    function test_generateResetPasswordCode_recordTalentResetPasswordCodeGeneratedEvent()
    {
        $this->client->clearRecordedEvents();
        $this->executeGenerateResetPasswordCode();
        $this->assertInstanceOf(ClientPasswordResetCodeGenerated::class,
            $this->client->getRecordedEvents()[0]);
    }

    private function executeActivate()
    {
        $this->client->activate($this->client->activationCode);
    }

    function test_activate_setActivatedTrue()
    {
        $this->executeActivate();
        $this->assertTrue($this->client->activated);
    }

    function test_activate_invalidActivationCode_throwEx()
    {
        $operation = function () {
            $this->client->activate('invalid code');
        };
        $errorDetail = 'bad request: invalid or expired token';
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }

    function test_activate_expiredToken_throwEx()
    {
        $this->client->activationCodeExpiredTime = new DateTimeImmutable("-24 hours");
        $operation = function () {
            $this->executeActivate();
        };
        $errorDetail = 'bad request: invalid or expired token';
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }

    function test_activate_talentHasNoToken_throwEx()
    {
        $this->client->activationCode = '';
        $this->client->activationCodeExpiredTime = null;
        $operation = function () {
            $this->executeActivate();
        };
        $errorDetail = 'bad request: invalid or expired token';
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }

    function test_activate_setActivationCodeAndExpiredDateNull()
    {
        $this->executeActivate();
        $this->assertNull($this->client->activationCode);
        $this->assertNull($this->client->activationCodeExpiredTime);
    }

    private function executeResetPassword()
    {
        $this->client->resetPassword($this->client->resetPasswordCode, $this->password);
    }

    function test_resetPassword_setPassword()
    {
        $this->executeResetPassword();
        $this->assertTrue($this->client->password->match($this->password));
    }

    function test_resetPassword_talentHasNoToken_throwEx()
    {
        $this->client->resetPasswordCode = '';
        $this->client->resetPasswordCodeExpiredTime = null;
        $operation = function () {
            $this->executeResetPassword();
        };
        $errorDetail = "bad request: invalid or expired token";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }

    function test_resetPassword_invalidToken_throwEx()
    {
        $operation = function () {
            $this->client->resetPassword('invalid token', $this->password);
        };
        $errorDetail = "bad request: invalid or expired token";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }

    function test_resetPassword_expiredToken_throwEx()
    {
        $this->client->resetPasswordCodeExpiredTime = new DateTimeImmutable("-24 hours");
        $operation = function () {
            $this->executeResetPassword();
        };
        $errorDetail = "bad request: invalid or expired token";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }

    function test_resetPassword_setResetCodeAndExpiredDateNull()
    {
        $this->executeResetPassword();
        $this->assertNull($this->client->resetPasswordCode);
        $this->assertNull($this->client->resetPasswordCodeExpiredTime);
    }

    function test_changePassword_changePasswordVO()
    {
        $this->client->changePassword($this->basePassword, $this->password);
        $this->assertTrue($this->client->password->match($this->password));
    }

    function test_changePassword_previousPasswordNotMatched_throwEx()
    {
        $operation = function () {
            $this->client->changePassword('not match', $this->password);
        };
        $errorDetail = 'forbidden: previous password not match';
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }

    function test_emailEquals_sameEmail_returnTrue()
    {
        $this->assertTrue($this->client->emailEquals($this->baseEmail));
    }

    function test_emailEquals_differentEmail_returnFalse()
    {
        $this->assertFalse($this->client->emailEquals('different_address@email.org'));
    }
    
    protected function executeCreateProgramRegistration()
    {
        return $this->client->createProgramRegistration($this->program);
    }
    
    public function test_createProgramRegistration_hasRegistrantInCollectionCorrespondToSameProgram_throwEx()
    {
        $this->programRegistration->expects($this->once())
                ->method('getProgram')
                ->willReturn($this->program);
        $operation = function (){
            $this->executeCreateProgramRegistration();
        };
        $errorDetail = "forbidden: you already registered to this program";
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_createProgramRegistration_programRegistrationInCollectionCorrespondToSameProgramAlreadyConcluded_processNormally()
    {
        $this->programRegistration->expects($this->once())
                ->method('getProgram')
                ->willReturn($this->program);
        $this->programRegistration->expects($this->once())
                ->method('isConcluded')
                ->willReturn(true);
        $this->executeCreateProgramRegistration();
        $this->markAsSuccess();
    }
    public function test_createProgramRegistration_hasActiveParticipantInCollectionCorrespontToSameProgram_throwEx()
    {
        $this->programParticipation->expects($this->once())
                ->method('getProgram')
                ->willReturn($this->program);
        $this->programParticipation->expects($this->once())
                ->method('isActive')
                ->willReturn(true);
        $operation = function (){
            $this->executeCreateProgramRegistration();
        };
        $errorDetail = "forbidden: you already participate in this program";
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_createProgramRegistration_programParticipationInCollectionCorrespontToSameProgramAlreadyInactive_processNormally()
    {
        $this->programParticipation->expects($this->once())
                ->method('getProgram')
                ->willReturn($this->program);
        $this->executeCreateProgramRegistration();
        $this->markAsSuccess();
    }

}

class TestableClient extends Client
{

    public $id, $password, $name, $email, $signupTime;
    public $activationCode, $activationCodeExpiredTime, $resetPasswordCode, $resetPasswordCodeExpiredTime, $activated;
    public $programRegistrations, $programParticipations;
}
