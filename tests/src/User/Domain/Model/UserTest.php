<?php

namespace User\Domain\Model;

use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Resources\ {
    DateTimeImmutableBuilder,
    Domain\ValueObject\PersonName
};
use SharedContext\Domain\Model\SharedEntity\FileInfoData;
use Tests\TestBase;
use User\Domain\ {
    Event\UserActivationCodeGenerated,
    Event\UserPasswordResetCodeGenerated,
    Model\User\ProgramParticipation,
    Model\User\ProgramRegistration,
    Model\User\UserFileInfo
};

class UserTest extends TestBase
{

    protected $user, $originalPassword = 'obatcovid19';
    
    protected $id = 'new-user-id', $firstName = 'firstname', $lastName = 'lastname', $email = 'new_user@email.org', 
            $password = 'password123';
    
    protected $programRegistration;
    protected $programParticipation;
    
    protected $programRegistrationId = 'programRegistrationId', $program, $programId = 'programId';
    
    protected $userFileInfoId = 'userFileInfoId', $fileInfoData;

    protected function setUp(): void
    {
        parent::setUp();

        $userData = new UserData('hadi', 'pranoto', 'covid19@hadipranoto.com', $this->originalPassword);
        $this->user = new TestableUser('id', $userData);
        $this->user->resetPasswordCode = bin2hex(random_bytes(32));
        $this->user->resetPasswordCodeExpiredTime = new DateTimeImmutable('+24 hours');
        $this->user->programRegistrations = new ArrayCollection();
        $this->user->programParticipations = new ArrayCollection();
        
        $this->programRegistration = $this->buildMockOfClass(ProgramRegistration::class);
        $this->user->programRegistrations->add($this->programRegistration);
        
        $this->programParticipation = $this->buildMockOfClass(ProgramParticipation::class);
        $this->user->programParticipations->add($this->programParticipation);
        
        $this->program = $this->buildMockOfInterface(ProgramInterface::class);
        $this->program->expects($this->any())->method('getId')->willReturn($this->programId);
        
        $this->fileInfoData = $this->buildMockOfClass(FileInfoData::class);
        $this->fileInfoData->expects($this->any())
                ->method('getName')
                ->willReturn('documents.pdf');
    }
    
    protected function getUserData()
    {
        return new UserData($this->firstName, $this->lastName, $this->email, $this->password);
    }

    private function executeConstruct()
    {
        return new TestableUser($this->id, $this->getUserData());
    }
    public function test_construct_setProperties()
    {
        $user = $this->executeConstruct();
        $this->assertEquals($this->id, $user->id);
        $this->assertEquals($this->email, $user->email);
        $this->assertTrue($user->password->match($this->password));
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $user->signupTime);
        $this->assertFalse($user->activated);
        $personName = new PersonName($this->firstName, $this->lastName);
        $this->assertEquals($personName, $user->personName);
    }
    public function test_construct_invalidEmail_throwEx()
    {
        $this->email = "invalid format";
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = "bad request: invalid email format";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    function test_construct_setActivationCodeAndExpiredTime()
    {
        $user = $this->executeConstruct();
        $this->assertNotEmpty($user->activationCode);
        $this->assertEquals((new DateTime('+24 hours'))->format("Y-m-d H:i:s"),
            $user->activationCodeExpiredTime->format('Y-m-d H:i:s'));
    }
    function test_construct_addTalentActivationCodeGeneratedEvent()
    {
        $user = $this->executeConstruct();
        $this->assertInstanceOf(UserActivationCodeGenerated::class, $user->pullRecordedEvents()[0]);
    }

    private function executeChangeProfile()
    {
        $this->user->changeProfile($this->firstName, $this->lastName);
    }
    function test_changeProfile_changePersonName()
    {
        $this->user->activated = true;
        $this->executeChangeProfile();
        $personName = new PersonName($this->firstName, $this->lastName);
        $this->assertEquals($personName, $this->user->personName);
    }
    public function test_changeProfile_inactiveAccount_forbiddenError()
    {
        $operation = function(){
            $this->executeChangeProfile();
        };
        $errorDetail = 'forbidden: inactive account';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }

    function test_generateActivationCode_setActivationCodeAndExpiredDateAlsoRecordTalentActivationCodeGeneratedEvent()
    {
        $this->user->activationCode = null;
        $this->user->activationCodeExpiredTime = null;
        $this->user->clearRecordedEvents();
        $this->user->generateActivationCode();
        $this->assertNotEmpty($this->user->activationCode);
        $this->assertEquals((new DateTime('+24 hours'))->format('Y-m-d H:i:s'),
            $this->user->activationCodeExpiredTime->format('Y-m-d H:i:s'));
        $this->assertInstanceOf(UserActivationCodeGenerated::class,
            $this->user->pullRecordedEvents()[0]);
    }
    function test_generateActivationCode_accountAlreadyActivated_forbiddenError()
    {
        $this->user->activate($this->user->activationCode);
        $operation = function () {
            $this->user->generateActivationCode();
        };
        $errorDetail = 'forbidden: account already activated';
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    private function executeGenerateResetPasswordCode()
    {
        $this->user->activated = true;
        $this->user->generateResetPasswordCode();
    }
    function test_generateResetPasswordCode_setResetPasswordCode()
    {
        $this->executeGenerateResetPasswordCode();
        $this->assertNotEmpty($this->user->resetPasswordCode);
    }
    function test_generateResetPasswordCode_setExpiredTime()
    {
        $this->executeGenerateResetPasswordCode();
        $this->assertEquals((new DateTime('+24 hours'))->format('Y-m-d H:i:s'),
            $this->user->resetPasswordCodeExpiredTime->format('Y-m-d H:i:s'));
    }
    function test_generateResetPasswordCode_recordTalentResetPasswordCodeGeneratedEvent()
    {
        $this->user->clearRecordedEvents();
        $this->executeGenerateResetPasswordCode();
        $this->assertInstanceOf(UserPasswordResetCodeGenerated::class,
            $this->user->pullRecordedEvents()[0]);
    }
    public function test_genearateResetPasswordCode_inactiveUser_forbiddenError()
    {
        $operation = function (){
            $this->user->generateResetPasswordCode();
        };
        $errorDetail = 'forbidden: inactive account';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }

    private function executeActivate()
    {
        $this->user->activate($this->user->activationCode);
    }
    function test_activate_setActivatedTrue()
    {
        $this->executeActivate();
        $this->assertTrue($this->user->activated);
    }
    function test_activate_invalidActivationCode_forbiddenError()
    {
        $operation = function () {
            $this->user->activate('invalid code');
        };
        $errorDetail = 'forbidden: invalid or expired token';
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    function test_activate_expiredToken_throwEx()
    {
        $this->user->activationCodeExpiredTime = new DateTimeImmutable("-24 hours");
        $operation = function () {
            $this->executeActivate();
        };
        $errorDetail = 'forbidden: invalid or expired token';
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    function test_activate_talentHasNoToken_throwEx()
    {
        $this->user->activationCode = '';
        $this->user->activationCodeExpiredTime = null;
        $operation = function () {
            $this->executeActivate();
        };
        $errorDetail = 'forbidden: invalid or expired token';
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    function test_activate_setActivationCodeAndExpiredDateNull()
    {
        $this->executeActivate();
        $this->assertNull($this->user->activationCode);
        $this->assertNull($this->user->activationCodeExpiredTime);
    }
    public function test_activate_alreadyActiveUser_forbiddenError()
    {
        $this->user->activated = true;
        $operation = function (){
            $this->executeActivate();
        };
        $errorDetail = 'forbidden: account already activated';
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }

    private function executeResetPassword()
    {
        $this->user->activated = true;
        $this->user->resetPassword($this->user->resetPasswordCode, $this->password);
    }
    function test_resetPassword_setPassword()
    {
        $this->executeResetPassword();
        $this->assertTrue($this->user->password->match($this->password));
    }
    function test_resetPassword_talentHasNoToken_throwEx()
    {
        $this->user->resetPasswordCode = '';
        $this->user->resetPasswordCodeExpiredTime = null;
        $operation = function () {
            $this->executeResetPassword();
        };
        $errorDetail = 'forbidden: invalid or expired token';
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    function test_resetPassword_invalidToken_throwEx()
    {
        $this->user->activated = true;
        $operation = function () {
            $this->user->resetPassword('invalid token', $this->password);
        };
        $errorDetail = 'forbidden: invalid or expired token';
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    function test_resetPassword_expiredToken_throwEx()
    {
        $this->user->resetPasswordCodeExpiredTime = new DateTimeImmutable("-24 hours");
        $operation = function () {
            $this->executeResetPassword();
        };
        $errorDetail = 'forbidden: invalid or expired token';
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    function test_resetPassword_setResetCodeAndExpiredDateNull()
    {
        $this->executeResetPassword();
        $this->assertNull($this->user->resetPasswordCode);
        $this->assertNull($this->user->resetPasswordCodeExpiredTime);
    }
    public function test_resetPassword_inactiveAccount_forbiddenError()
    {
        $operation = function (){
            $this->user->resetPassword($this->user->resetPasswordCode, $this->password);
        };
        $errorDetail = 'forbidden: inactive account';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }

    function test_changePassword_changePasswordVO()
    {
        $this->user->activated = true;
        $this->user->changePassword($this->originalPassword, $this->password);
        $this->assertTrue($this->user->password->match($this->password));
    }
    function test_changePassword_previousPasswordNotMatched_throwEx()
    {
        $this->user->activated = true;
        $operation = function () {
            $this->user->changePassword('not match', $this->password);
        };
        $errorDetail = 'forbidden: previous password not match';
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_changePassword_inactiveAccount_forbiddenError()
    {
        $operation = function (){
            $this->user->changePassword($this->originalPassword, $this->password);
        };
        $errorDetail = 'forbidden: inactive account';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    
    protected function executeRegisterToProgram()
    {
        $this->user->activated = true;
        return $this->user->registerToProgram($this->programRegistrationId, $this->program);
    }
    public function test_registerToProgram_returnProgramRegistration()
    {
        $this->program->expects($this->any())
                ->method('isRegistrationOpenFor')
                ->willReturn(true);
        $programRegistration = new ProgramRegistration($this->user, $this->programRegistrationId, $this->program);
        $this->assertEquals($programRegistration, $this->executeRegisterToProgram());
    }
    public function test_registerToProgram_inactiveUser_forbiddenError()
    {
        $this->user->activated = false;
        $operation = function (){
            $this->user->registerToProgram($this->programRegistrationId, $this->program);
        };
        $errorDetail = 'forbidden: inactive account';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_registerToProgram_alreadyRegisteredToSameProgram_forbiddenError()
    {
        $this->programRegistration->expects($this->once())
                ->method('isUnconcludedRegistrationToProgram')
                ->with($this->program)
                ->willReturn(true);
        $operation = function (){
            $this->executeRegisterToProgram();
        };
        $errorDetail = 'forbidden: you already registered to this program';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_registerToProgram_alreadyActiveParticipantOfSameProgram_forbiddenError()
    {
        $this->programParticipation->expects($this->once())
                ->method('isActiveParticipantInProgram')
                ->with($this->program)
                ->willReturn(true);
        $operation = function (){
            $this->executeRegisterToProgram();
        };
        $errorDetail = 'forbidden: you already participate in this program';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
        
    }
    
    public function test_createUserFileInfo_returnUserFileInfo()
    {
        $userFileInfo = new UserFileInfo($this->user, $this->userFileInfoId, $this->fileInfoData);
        $this->assertEquals($userFileInfo, $this->user->createUserFileInfo($this->userFileInfoId, $this->fileInfoData));
    }

}

class TestableUser extends User
{

    public $id, $password, $personName, $email, $signupTime;
    public $activationCode, $activationCodeExpiredTime, $resetPasswordCode, $resetPasswordCodeExpiredTime, $activated;
    public $programRegistrations;
    public $programParticipations;
}
