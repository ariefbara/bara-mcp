<?php

namespace Client\Dommain\Model;

use Client\Domain\ {
    Event\ClientActivationCodeGenerated,
    Event\ClientResetPasswordCodeGenerated,
    Model\Client,
    Model\Client\ClientFileInfo,
    Model\Client\ProgramParticipation,
    Model\Client\ProgramRegistration,
    Model\ProgramInterface
};
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Resources\ {
    DateTimeImmutableBuilder,
    Domain\ValueObject\Password,
    Domain\ValueObject\PersonName
};
use SharedContext\Domain\Model\SharedEntity\FileInfoData;
use Tests\TestBase;

class ClientTest extends TestBase
{
    protected $client, $password;
    protected $firstName = 'firstname', $lastName = 'lastname', $previousPassword = 'previous123', $newPassword = 'newPwd123';
    
    protected $programRegistration;
    protected $programParticipation;
    
    protected $programRegistrationId = 'programRegistrationId';
    protected $program;
    
    protected $clientFileInfoId = 'clientFileInfoId', $fileInfoData;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->client = new TestableClient();
        
        $this->password = $this->buildMockOfClass(Password::class);
        $this->client->password = $this->password;
        
        $this->client->activationCode = bin2hex(random_bytes(32));
        $this->client->activationCodeExpiredTime = new DateTimeImmutable('+24 hours');
        $this->client->resetPasswordCode = bin2hex(random_bytes(32));
        $this->client->resetPasswordCodeExpiredTime = new DateTimeImmutable('+24 hours');
        $this->client->activated = true;
        $this->client->programRegistrations = new ArrayCollection();
        $this->client->programParticipations = new ArrayCollection();
        
        $this->programRegistration = $this->buildMockOfClass(ProgramRegistration::class);
        $this->client->programRegistrations->add($this->programRegistration);
        
        $this->programParticipation = $this->buildMockOfClass(ProgramParticipation::class);
        $this->client->programParticipations->add($this->programParticipation);
        
        $this->program = $this->buildMockOfInterface(ProgramInterface::class);
        $this->program->expects($this->any())->method('isRegistrationOpenFor')->willReturn(true);
        
        $this->fileInfoData = $this->buildMockOfClass(FileInfoData::class);
        $this->fileInfoData->expects($this->any())->method('getName')->willReturn('docs.pdf');
    }
    
    protected function executeUpdateProfile()
    {
        $this->client->updateProfile($this->firstName, $this->lastName);
    }
    
    public function test_updateProfile_changeName()
    {
        $this->executeUpdateProfile();
        $personName = new PersonName($this->firstName, $this->lastName);
        $this->assertEquals($personName, $this->client->personName);
    }
    public function test_updateProfile_inactiveClient_forbiddenError()
    {
        $this->client->activated = false;
        $operation = function (){
            $this->executeUpdateProfile();
        };
        $errorDetail = 'forbidden: only active client can  make this request';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    
    protected function executeChangePassword()
    {
        $this->password->expects($this->any())
                ->method('match')
                ->with($this->previousPassword)
                ->willReturn(true);
        
        $this->client->changePassword($this->previousPassword, $this->newPassword);
    }
    public function test_changePassword()
    {
        $this->executeChangePassword();
        $this->assertTrue($this->client->password->match($this->newPassword));
    }
    public function test_changePassword_unmatchPreviousPassword_forbiddenError()
    {
        $this->password->expects($this->once())
                ->method('match')
                ->willReturn(false);
        $operation = function (){
            $this->executeChangePassword();
        };
        $errorDetail = 'forbidden: previous password not match';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_changePassword_inactiveClient_forbiddenError()
    {
        $this->client->activated = false;
        $operation = function (){
            $this->executeChangePassword();
        };
        $errorDetail = 'forbidden: only active client can  make this request';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    
    protected function executeActivate()
    {
        $this->client->activated = false;
        $this->client->activate($this->client->activationCode);
    }
    public function test_activate_activateAccount()
    {
        $this->executeActivate();
        $this->assertTrue($this->client->activated);
    }
    public function test_activate_resetActivationCodeAndActivationCodeExpiredTime()
    {
        $this->executeActivate();
        $this->assertNull($this->client->activationCode);
        $this->assertNull($this->client->activationCodeExpiredTime);
    }
    public function test_activate_activationCodeUnmatch_forbiddenError()
    {
        $this->client->activated = false;
        $operation = function (){
            $this->client->activate('unmatchCode');
        };
        $errorDetail = 'forbidden: activation code not match or expired';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_activate_activationCodeExpired_forbiddenError()
    {
        $this->client->activationCodeExpiredTime = new \DateTimeImmutable('-1 second');
        $operation = function (){
            $this->executeActivate();
        };
        $errorDetail = 'forbidden: activation code not match or expired';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_activate_accountAlreadyActivated_forbiddenError()
    {
        $this->client->activated = true;
        $operation = function (){
            $this->client->activate($this->client->activationCode);
        };
        $errorDetail = 'forbidden: account already activated';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_activate_emptyActivationCode_forbiddenError()
    {
        $this->client->activationCode = "";
        $operation = function (){
            $this->executeActivate();
        };
        $errorDetail = 'forbidden: activation code not match or expired';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    
    protected function executeResetPassword()
    {
        $this->client->resetPassword($this->client->resetPasswordCode, $this->newPassword);
    }
    public function test_resetPassword_changePassword()
    {
        $this->executeResetPassword();
        $this->assertTrue($this->client->password->match($this->newPassword));
    }
    public function test__resetPassword_emptyResetPasswordCodeAndExpiredTime()
    {
        $this->executeResetPassword();
        $this->assertNull($this->client->resetPasswordCode);
        $this->assertNull($this->client->resetPasswordCodeExpiredTime);
    }
    public function test_resetPassword_unmatchResetPasswordCode_forbiddenError()
    {
        $operation = function (){
            $this->client->resetPassword('unmatch', $this->newPassword);
        };
        $errorDetail = 'forbidden: reset password code not match or expired';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_resetPassword_emptyResetPasswordCode_forbiddenError()
    {
        $this->client->resetPasswordCode = '';
        $operation = function (){
            $this->executeResetPassword();
        };
        $errorDetail = 'forbidden: reset password code not match or expired';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_resetPassword_resetPasswordCodeExpired_forbiddenError()
    {
        $this->client->resetPasswordCodeExpiredTime = new \DateTimeImmutable('-1 second');
        $operation = function (){
            $this->executeResetPassword();
        };
        $errorDetail = 'forbidden: reset password code not match or expired';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_resetPassword_inactiveAccount_forbiddenError()
    {
        $this->client->activated = false;
        $operation = function (){
            $this->executeResetPassword();
        };
        $errorDetail = 'forbidden: only active client can  make this request';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    
    protected function executeGenerateActivationCode()
    {
        $this->client->activated = false;
        $this->client->generateActivationCode();
    }
    public function test_generateActivationCode_generateActivationCodeAndExpiredTime()
    {
        $this->client->activationCode = null;
        $this->client->activationCodeExpiredTime = null;
        $this->executeGenerateActivationCode();
        $this->assertNotEmpty($this->client->activationCode);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy('+24 hours'), $this->client->activationCodeExpiredTime);
    }
    public function test_generateActivationCode_accountAlreadyActivated_forbiddenError()
    {
        $this->client->activated = true;
        $operation = function (){
            $this->client->generateActivationCode();
        };
        $errorDetail = 'forbidden: account already activated';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_generateActivationCode_recordClientActivationCodeGeneratedEvent()
    {
        $event = new ClientActivationCodeGenerated($this->client->firmId, $this->client->id);
        $this->executeGenerateActivationCode();
        $this->assertEquals($event, $this->client->recordedEvents[0]);
    }
    
    protected function executeGenerateResetPasswordCode()
    {
        $this->client->generateResetPasswordCode();
    }
    public function test_generateResetPasswordCode_setResetPasswordCodeAndExpiredTime()
    {
        $this->client->resetPasswordCode = null;
        $this->client->resetPasswordCodeExpiredTime= null;
        
        $this->executeGenerateResetPasswordCode();
        $this->assertNotEmpty($this->client->resetPasswordCode);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy('+24 hours'), $this->client->resetPasswordCodeExpiredTime);
    }
    public function test_generateResetPasswordCode_inactiveAccount_forbiddenError()
    {
        $this->client->activated = false;
        $operation = function (){
            $this->executeGenerateResetPasswordCode();
        };
        $errorDetail = 'forbidden: only active client can  make this request';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_generateResetPasswordCode_storeClientResetPasswordCodeGeneratedEvent()
    {
        $event = new ClientResetPasswordCodeGenerated($this->client->firmId, $this->client->id);
        $this->executeGenerateResetPasswordCode();
        $this->assertEquals($event, $this->client->recordedEvents[0]);
    }
    
    protected function executeRegisterToProgram()
    {
        $this->program->expects($this->any())
                ->method('firmIdEquals')
                ->willReturn(true);
        return $this->client->registerToProgram($this->programRegistrationId, $this->program);
    }
    
    public function test_executeRegisterToProgram_returnProgramRegistration()
    {
        $programRegistration = new ProgramRegistration($this->client, $this->programRegistrationId, $this->program);
        $this->assertEquals($programRegistration, $this->executeRegisterToProgram());
    }
    public function test_registerToProgram_inactiveClient_forbiddenError()
    {
        $this->client->activated = false;
        $operation = function (){
            $this->executeRegisterToProgram();
        };
        $errorDetail = 'forbidden: only active client can  make this request';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_registerToProgram_haveUnconcludedRegistrationToSameProgram_forbiddenError()
    {
        $this->programRegistration->expects($this->once())
                ->method('isUnconcludedRegistrationToProgram')
                ->with($this->program)
                ->willReturn(true);
        $operation = function (){
            $this->executeRegisterToProgram();
        };
        $errorDetail = 'forbidden: client already registered to this program';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_registerToProgram_alreadyActiveParticipantOfSameProgram_forbiddenError()
    {
        $this->programParticipation->expects($this->once())
                ->method('isActiveParticipantOfProgram')
                ->with($this->program)
                ->willReturn(true);
        $operation = function (){
            $this->executeRegisterToProgram();
        };
        $errorDetail = 'forbidden: client already active participant of this program';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_registerToProgram_programFromDifferentFirm_forbiddenError()
    {
        $this->program->expects($this->once())
                ->method('firmIdEquals')
                ->with($this->client->firmId)
                ->willReturn(false);
        $operation = function (){
            $this->executeRegisterToProgram();
        };
        $errorDetail = 'forbidden: cannot register to program from different firm';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    
    public function test_createClientFileInfo_returnClientFileInfo()
    {
        $clientFileInfo = new ClientFileInfo($this->client, $this->clientFileInfoId, $this->fileInfoData);
        $this->assertEquals($clientFileInfo, $this->client->createClientFileInfo($this->clientFileInfoId, $this->fileInfoData));
    }
}

class TestableClient extends Client
{
    public $firmId = 'firmId';
    public $id = 'clientId';
    public $personName;
    public $email;
    public $password;
    public $activationCode;
    public $activationCodeExpiredTime;
    public $resetPasswordCode;
    public $resetPasswordCodeExpiredTime;
    public $activated;
    public $programRegistrations;
    public $programParticipations;
    
    public $recordedEvents;
    
    function __construct()
    {
        parent::__construct();
    }
}
