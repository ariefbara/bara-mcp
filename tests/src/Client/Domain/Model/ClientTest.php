<?php

namespace Client\Dommain\Model;

use Client\Domain\DependencyModel\Firm\BioForm;
use Client\Domain\Model\Client;
use Client\Domain\Model\Client\ClientBio;
use Client\Domain\Model\Client\ClientFileInfo;
use Client\Domain\Model\Client\ProgramParticipation;
use Client\Domain\Model\Client\ProgramRegistration;
use Client\Domain\Model\ClientData;
use Client\Domain\Model\ProgramInterface;
use Config\EventList;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Query\Domain\Model\Firm;
use Resources\DateTimeImmutableBuilder;
use Resources\Domain\Event\CommonEvent;
use Resources\Domain\ValueObject\Password;
use Resources\Domain\ValueObject\PersonName;
use SharedContext\Domain\Model\SharedEntity\FileInfoData;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class ClientTest extends TestBase
{
    protected $client;
    protected $firm, $firmId = 'firmId';

    protected $id = 'newClientId', $firstName = 'hadi', $lastName = 'pranoto', $email = 'covid@hadipranoto.com', 
            $password = 'obatcovid19';
    
    protected $previousPassword = 'previous123', $newPassword = 'newPwd123';
    
    protected $programRegistration;
    protected $programParticipation;
    
    protected $programRegistrationId = 'programRegistrationId';
    protected $program;
    
    protected $clientFileInfoId = 'clientFileInfoId', $fileInfoData;
    protected $bioForm, $clientBio;
    protected $formRecordData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->firm = $this->buildMockOfClass(Firm::class);
        $this->firm->expects($this->any())->method('getId')->willReturn($this->firmId);
        
        $clientData = new ClientData('firstname', 'lastname', 'client@email.org', 'password12312');
        $this->client = new TestableClient($this->firm, 'id', $clientData);        
        $this->client->password = $this->buildMockOfClass(Password::class);
        
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
        
        $this->bioForm = $this->buildMockOfClass(BioForm::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        $this->clientBio = $this->buildMockOfClass(ClientBio::class);
        
        $this->client->clientBios = new ArrayCollection();
        $this->client->clientBios->add($this->clientBio);
    }
    protected function assertInactiveClientForbiddenError(callable $operation): void
    {
        $errorDetail = "forbidden: only active client can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
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
        $this->assertEquals($this->firmId, $client->firmId);
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
        $errorDetail = 'forbidden: only active client can make this request';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    
    protected function executeChangePassword()
    {
        $this->client->password->expects($this->any())
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
        $this->client->password->expects($this->once())
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
        $errorDetail = 'forbidden: only active client can make this request';
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
        $errorDetail = 'forbidden: only active client can make this request';
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
        $this->client->recordedEvents = [];
        $event = new CommonEvent(EventList::CLIENT_ACTIVATION_CODE_GENERATED, $this->client->id);
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
        $errorDetail = 'forbidden: only active client can make this request';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_generateResetPasswordCode_storeClientResetPasswordCodeGeneratedEvent()
    {
        $this->client->recordedEvents = [];
        $event = new CommonEvent(EventList::CLIENT_RESET_PASSWORD_CODE_GENERATED, $this->client->id);
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
        $errorDetail = 'forbidden: only active client can make this request';
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
    
    protected function executeSubmitBio()
    {
        $this->bioForm->expects($this->any())
                ->method("belongsToFirm")
                ->willReturn(true);
        return $this->client->submitBio($this->bioForm, $this->formRecordData);
    }
    public function test_submitBio_addClientBioToCollection()
    {
        $this->executeSubmitBio();
        $this->assertEquals(2, $this->client->clientBios->count());
        $this->assertInstanceOf(ClientBio::class, $this->client->clientBios->last());
    }
    public function test_submitBio_alredyHasBioCorrespondWithSameForm_updateExistingBio()
    {
        $this->clientBio->expects($this->once())
                ->method("isActiveBioCorrespondWithForm")
                ->with($this->bioForm)
                ->willReturn(true);
        $this->clientBio->expects($this->once())
                ->method("update")
                ->with($this->formRecordData);
        $this->executeSubmitBio();
    }
    public function test_submitBio_alreadyHasBioCorrespondWithSameForm_preventAddNewBio()
    {
        $this->clientBio->expects($this->once())
                ->method("isActiveBioCorrespondWithForm")
                ->willReturn(true);
        $this->executeSubmitBio();
        $this->assertEquals(1, $this->client->clientBios->count());
    }
    public function test_submitBio_inactiveAccount_forbidden()
    {
        $this->client->activated = false;
        $this->assertInactiveClientForbiddenError(function (){
            $this->executeSubmitBio();
        });
    }
    public function test_submitBio_bioFormNotFromSameFirm_forbidden()
    {
        $this->bioForm->expects($this->once())
                ->method("belongsToFirm")
                ->with($this->client->firmId)
                ->willReturn(false);
        $operation = function (){
            $this->executeSubmitBio();
        };
        $errorDetail = "forbidden: can only use asset in same firm";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeRemoveBio()
    {
        $this->clientBio->expects($this->any())
                ->method("belongsToClient")
                ->willReturn(true);
        $this->client->removeBio($this->clientBio);
    }
    public function test_removeBio_removeBio()
    {
        $this->clientBio->expects($this->once())->method("remove");
        $this->executeRemoveBio();
    }
    public function test_removeBio_inactiveClient()
    {
        $this->client->activated = false;
        $this->assertInactiveClientForbiddenError(function (){
            $this->executeRemoveBio();
        });
    }
    public function test_removeBio_bioNotBelongsToClient_forbidden()
    {
        $this->clientBio->expects($this->once())
                ->method("belongsToClient")
                ->with($this->client)
                ->willReturn(false);
        $operation = function (){
            $this->executeRemoveBio();
        };
        $errorDetail = "forbidden: can only manage owned asset";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
}

class TestableClient extends Client
{
    public $firmId = 'firmId';
    public $id = 'clientId';
    public $personName;
    public $email;
    public $password;
    public $signupTime;
    public $activationCode;
    public $activationCodeExpiredTime;
    public $resetPasswordCode;
    public $resetPasswordCodeExpiredTime;
    public $activated;
    public $programRegistrations;
    public $programParticipations;
    public $clientBios;
    
    public $recordedEvents;
    
}
