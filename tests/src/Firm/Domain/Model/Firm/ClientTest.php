<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Program\Mission;
use Firm\Domain\Model\Firm\Program\Mission\MissionComment;
use Firm\Domain\Model\Firm\Program\Mission\MissionCommentData;
use Resources\Domain\ValueObject\PersonName;
use Tests\TestBase;

class ClientTest extends TestBase
{
    protected $firm;
    protected $client, $personName, $fullName = 'client name';
    
    protected $id = 'newId', $firstname = 'newfirstname', $lastname = 'newlastname', $email = 'newclient@email.org', 
            $password = 'newpassword123';

    protected $mission, $missionComment, $missionCommentId = 'missionCommentId', $missionCommentData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->firm = $this->buildMockOfClass(Firm::class);
        $clientRegistrationData = new ClientRegistrationData('firstname', 'lastname', 'client@email.org', 'password123');
        $this->client = new TestableClient($this->firm, 'id', $clientRegistrationData);
        $this->client->activated = false;
        
        $this->personName = $this->buildMockOfClass(PersonName::class);
        $this->personName->expects($this->any())->method('getFullName')->willReturn($this->fullName);
        $this->client->personName = $this->personName;
        
        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->missionComment = $this->buildMockOfClass(MissionComment::class);
        $this->missionCommentData = $this->buildMockOfClass(MissionCommentData::class);
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
        $this->assertEquals(\Resources\DateTimeImmutableBuilder::buildYmdHisAccuracy(), $client->signupTime);
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
        $this->client->activationCodeExpiredTime = new \Monolog\DateTimeImmutable('+1 hours');
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
    
}
