<?php

namespace Firm\Domain\Model\Firm;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Program\ClientParticipant;
use Firm\Domain\Model\Firm\Program\Mission;
use Firm\Domain\Model\Firm\Program\Mission\MissionComment;
use Firm\Domain\Model\Firm\Program\Mission\MissionCommentData;
use Resources\DateTimeImmutableBuilder;
use Resources\Domain\ValueObject\PersonName;
use Tests\TestBase;

class ClientTest extends TestBase
{
    protected $firm;
    protected $client, $personName, $fullName = 'client name';
    protected $clientParticipant;
    protected $clientRegistrant;

    protected $id = 'newId', $firstname = 'newfirstname', $lastname = 'newlastname', $email = 'newclient@email.org', 
            $password = 'newpassword123';

    protected $mission, $missionComment, $missionCommentId = 'missionCommentId', $missionCommentData;
    protected $program;
    //
    protected $participant, $participantId = 'participant-id';
    //
    protected $registrantId = 'registrantId', $registrant;
    //
    protected $participantTypes;

    protected function setUp(): void
    {
        parent::setUp();
        $this->firm = $this->buildMockOfClass(Firm::class);
        $clientRegistrationData = new ClientRegistrationData('firstname', 'lastname', 'client@email.org', 'password123');
        $this->client = new TestableClient($this->firm, 'id', $clientRegistrationData);
        $this->client->activated = false;
        
        $this->clientParticipant = $this->buildMockOfClass(ClientParticipant::class);
        $this->client->clientParticipants = new ArrayCollection();
        $this->client->clientParticipants->add($this->clientParticipant);
        
        $this->clientRegistrant = $this->buildMockOfClass(Client\ClientRegistrant::class);
        $this->client->clientRegistrants = new ArrayCollection();
        $this->client->clientRegistrants->add($this->clientRegistrant);
        
        $this->personName = $this->buildMockOfClass(PersonName::class);
        $this->personName->expects($this->any())->method('getFullName')->willReturn($this->fullName);
        $this->client->personName = $this->personName;
        
        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->missionComment = $this->buildMockOfClass(MissionComment::class);
        $this->missionCommentData = $this->buildMockOfClass(MissionCommentData::class);
        
        $this->program = $this->buildMockOfClass(Program::class);
        //
        $this->participant = $this->buildMockOfClass(Program\Participant::class);
        //
        $this->registrant = $this->buildMockOfClass(Program\Registrant::class);
        //
        $this->participantTypes = $this->buildMockOfClass(\Query\Domain\Model\Firm\ParticipantTypes::class);
    }
    
    protected function getClientRegistrationData()
    {
        return new ClientRegistrationData($this->firstname, $this->lastname, $this->email, $this->password);
    }
    
    protected function construct()
    {
        return new TestableClient($this->firm, $this->id, $this->getClientRegistrationData());
    }
    public function test_construct_setProperties()
    {
        $client = $this->construct();
        $this->assertSame($this->firm, $client->firm);
        $this->assertSame($this->id, $client->id);
        $this->assertEquals(new PersonName($this->firstname, $this->lastname), $client->personName);
        $this->assertSame($this->email, $client->email);
        $this->assertTrue($client->password->match($this->password));
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $client->signupTime);
        $this->assertTrue($client->activated);
        $this->assertNull($client->activationCode);
        $this->assertNull($client->activationCodeExpiredTime);
    }
    public function test_construct_invalidEmailFormat_badRequest()
    {
        $this->email = 'invalid format';
        $this->assertRegularExceptionThrowed(function() {
            $this->construct();
        }, 'Bad Request', 'bad request: client email must be a valid mail address');
    }
    
    protected function assertManageableInFirm()
    {
        $this->client->assertManageableInFirm($this->firm);
    }
    public function test_assertManageableInFirm_sameFirm_void()
    {
        $this->assertManageableInFirm();
        $this->markAsSuccess();
    }
    public function test_assertManageableInFirm_differentFirm_forbidden()
    {
        $this->client->firm = $this->buildMockOfClass(Firm::class);
        $this->assertRegularExceptionThrowed(function() {
            $this->assertManageableInFirm();
        }, 'Forbidden', 'forbidden: can only manage client in same firm');
    }
    
    protected function assertUsableInFirm()
    {
        $this->client->assertUsableInFirm($this->firm);
    }
    public function test_assertUsableInFirm_activatedClientOnSameFirm_void()
    {
        $this->client->activated = true;
        $this->assertUsableInFirm();
        $this->markAsSuccess();
    }
    public function test_assertUsableInFirm_inactiveClient_forbidden()
    {
        $this->assertRegularExceptionThrowed(function() {
            $this->assertUsableInFirm();
        }, 'Forbidden', 'forbidden: only activated client is usable');
    }
    public function test_assertUsableInFirm_differentFirm_forbidden()
    {
        $this->client->activated = true;
        $this->client->firm = $this->buildMockOfClass(Firm::class);
        $this->assertRegularExceptionThrowed(function() {
            $this->assertUsableInFirm();
        }, 'Forbidden', 'forbidden: can only used client from same firm');
    }
    
    protected function activate()
    {
        $this->client->activationCode = 'random-activation-string';
        $this->client->activationCodeExpiredTime = new DateTimeImmutable('+1 hours');
        $this->client->activate();
    }
    public function test_activate_setActivatedAndResetAllActivationRelatedData()
    {
        $this->activate();
        $this->assertTrue($this->client->activated);
        $this->assertNull($this->client->activationCode);
        $this->assertNull($this->client->activationCodeExpiredTime);
    }
    
    protected function executeSubmitCommentInMission()
    {
        $this->client->submitCommentInMission($this->mission, $this->missionCommentId, $this->missionCommentData);
    }
    public function test_submitCommentInMission_returnMissionsReceiveCommentResult()
    {
        $this->mission->expects($this->once())
                ->method('receiveComment')
                ->with($this->missionCommentId, $this->missionCommentData, $this->client->id, $this->fullName);
        $this->executeSubmitCommentInMission();
    }
    
    protected function executeReplyMissionComment()
    {
        $this->client->replyMissionComment($this->missionComment, $this->missionCommentId, $this->missionCommentData);
    }
    public function test_replyMissionComment_returnMissionCommentssReceiveReplyResult()
    {
        $this->missionComment->expects($this->once())
                ->method('receiveReply')
                ->with($this->missionCommentId, $this->missionCommentData, $this->client->id, $this->fullName);
        $this->executeReplyMissionComment();
    }
    
    protected function addIntoProgram()
    {
        return $this->client->addIntoProgram($this->program);
    }
    public function test_addIntoProgram_addClientParticipantToCollection()
    {
        $this->addIntoProgram();
        $this->assertEquals(2, $this->client->clientParticipants->count());
        $this->assertInstanceOf(ClientParticipant::class, $this->client->clientParticipants->last());
    }
    public function test_addIntoProgram_alreadyParticipateInSameProgram_enableCurrentParticipation()
    {
        $this->clientParticipant->expects($this->once())
                ->method('correspondWithProgram')
                ->with($this->program)
                ->willReturn(true);
        $this->clientParticipant->expects($this->once())
                ->method('enable');
        $this->addIntoProgram();
    }
    public function test_addIntoProgram_alreadyParticipateInSameProgram_preventAddNewClient()
    {
        $this->clientParticipant->expects($this->once())
                ->method('correspondWithProgram')
                ->with($this->program)
                ->willReturn(true);
        $this->addIntoProgram();
        $this->assertEquals(1, $this->client->clientParticipants->count());
    }
    public function test_addIntoProgram_returnClientParticipantId()
    {
        $this->clientParticipant->expects($this->once())
                ->method('correspondWithProgram')
                ->with($this->program)
                ->willReturn(true);
        $this->clientParticipant->expects($this->once())
                ->method('getId')
                ->willReturn($clientParticipantId = 'clientParticipantId');
        $this->assertSame($clientParticipantId, $this->addIntoProgram());
    }
    public function test_addIntoProgram_assertProgramCanAcceptClientType()
    {
        $this->program->expects($this->once())
                ->method('assertCanAcceptParticipantOfType')
                ->with('client');
        $this->addIntoProgram();
    }
    
    protected function assertBelongsInFirm()
    {
        $this->client->assertBelongsInFirm($this->firm);
    }
    public function test_assertBelongsInFirm_sameFirm_void()
    {
        $this->assertBelongsInFirm();
        $this->markAsSuccess();
    }
    public function test_assertBelongsInFirm_differentFirm_forbidden()
    {
        $this->client->firm = $this->buildMockOfClass(Firm::class);
        $this->assertRegularExceptionThrowed(function(){
            $this->assertBelongsInFirm();
        }, 'Forbidden', 'client is from different firm');
    }
    
    protected function getClientCustomerInfo()
    {
        return $this->client->getClientCustomerInfo();
    }
    public function test_getClientCustomerInfo_returnCustomerInfo()
    {
        $customerInfo = new \SharedContext\Domain\ValueObject\CustomerInfo($this->fullName, $this->client->email);
        $this->assertEquals($customerInfo, $this->getClientCustomerInfo());
    }
    
    //
    protected function addProgramParticipation()
    {
        $this->client->addProgramParticipation($this->participantId, $this->participant);
    }
    public function test_addProgramParticipant_addClientParticipant()
    {
        $this->addProgramParticipation();
        $this->assertEquals(2, $this->client->clientParticipants->count());
    }
    
    //
    protected function addProgramRegistration()
    {
        $this->client->addProgramRegistration($this->registrantId, $this->registrant);
    }
    public function test_addProgramRegistration_addClientRegistrantToCollection()
    {
        $this->addProgramRegistration();
        $this->assertEquals(2, $this->client->clientRegistrants->count());
        $this->assertEquals(new Client\ClientRegistrant($this->client, $this->registrantId, $this->registrant), $this->client->clientRegistrants->last());
    }
    
    //
    protected function assertNoActiveParticipationOrOngoingRegistrationInProgram()
    {
        $this->client->assertNoActiveParticipationOrOngoingRegistrationInProgram($this->program);
    }
    public function test_assertNoActiveParticipationOrOngoingRegistrationInProgram_hasUnconcludedRegistrationInSameProgram_forbidden()
    {
        $this->clientRegistrant->expects($this->any())
                ->method('isUnconcludedRegistrationInProgram')
                ->with($this->program)
                ->willReturn(true);
        $this->assertRegularExceptionThrowed(function () {
            $this->assertNoActiveParticipationOrOngoingRegistrationInProgram();
        }, 'Forbidden', 'program application refused, client has unconcluded registration in same program');
    }
    public function test_assertNoActiveParticipationOrOngoingRegistrationInProgram_noUnconcludedRegistrationInSameProgram_void()
    {
        $this->assertNoActiveParticipationOrOngoingRegistrationInProgram();
        $this->markAsSuccess();
    }
    public function test_assertNoActiveParticipationOrOngoingRegistrationInProgram_hasActiveParticipationInSameProgram_forbidden()
    {
        $this->clientParticipant->expects($this->any())
                ->method('isActiveParticipantInProgram')
                ->with($this->program)
                ->willReturn(true);
        $this->assertRegularExceptionThrowed(function () {
            $this->assertNoActiveParticipationOrOngoingRegistrationInProgram();
        }, 'Forbidden', 'program application refused, client is active participant in same program');
    }
    public function test_assertNoActiveParticipationOrOngoingRegistrationInProgram_noActiveRegistrationInSameProgram_void()
    {
        $this->assertNoActiveParticipationOrOngoingRegistrationInProgram();
        $this->markAsSuccess();
    }
    
    //
    protected function assertTypeIncludedIn()
    {
        $this->participantTypes->expects($this->any())
                ->method('hasType')
                ->with(\Query\Domain\Model\Firm\ParticipantTypes::CLIENT_TYPE)
                ->willReturn(true);
        $this->client->assertTypeIncludedIn($this->participantTypes);
    }
    public function test_assertTypeInclucedIn_clientTypeIsNotIncludedInParticipantType_forbidden()
    {
        $this->participantTypes->expects($this->once())
                ->method('hasType')
                ->with(\Query\Domain\Model\Firm\ParticipantTypes::CLIENT_TYPE)
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function (){
            $this->assertTypeIncludedIn();
        }, 'Forbidden', 'program application refused, client type is not accomodate in program');
    }
    public function test_assertTypeInclucedIn_clientTypeIsIncludedInParticipantType_void()
    {
        $this->assertTypeIncludedIn();
        $this->markAsSuccess();
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
    public $activated;
    public $activationCode;
    public $activationCodeExpiredTime;
    public $clientParticipants;
    public $clientRegistrants;
}
