<?php

namespace Firm\Domain\Model\Firm;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Client\ClientParticipant;
use Firm\Domain\Model\Firm\Program\Mission;
use Firm\Domain\Model\Firm\Program\Mission\MissionComment;
use Firm\Domain\Model\Firm\Program\Mission\MissionCommentData;
use Firm\Domain\Model\Firm\Program\Participant;
use Query\Domain\Model\Firm\ParticipantTypes;
use Resources\DateTimeImmutableBuilder;
use Resources\Domain\ValueObject\PersonName;
use Tests\TestBase;

class ClientTest extends TestBase
{
    protected $firm;
    protected $client, $personName, $fullName = 'client name';
    protected $clientParticipant;

    protected $id = 'newId', $firstname = 'newfirstname', $lastname = 'newlastname', $email = 'newclient@email.org', 
            $password = 'newpassword123';

    protected $mission, $missionComment, $missionCommentId = 'missionCommentId', $missionCommentData;
    protected $clientParticipantId = 'clientParticipantId', $program;
    //
    protected $participant, $participantId = 'participant-id';

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
        
        $this->personName = $this->buildMockOfClass(PersonName::class);
        $this->personName->expects($this->any())->method('getFullName')->willReturn($this->fullName);
        $this->client->personName = $this->personName;
        
        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->missionComment = $this->buildMockOfClass(MissionComment::class);
        $this->missionCommentData = $this->buildMockOfClass(MissionCommentData::class);
        
        $this->program = $this->buildMockOfClass(Program::class);
        //
        $this->participant = $this->buildMockOfClass(Participant::class);
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
    
    protected function addAsProgramApplicant()
    {
        return $this->client->addAsProgramApplicant($this->clientParticipantId, $this->program);
    }
    public function test_addAsProgramApplicant_returnClientParticipant()
    {
        $this->program->expects($this->once())
                ->method('receiveApplication')
                ->with($this->clientParticipantId, ParticipantTypes::CLIENT_TYPE)
                ->willReturn($participant = $this->buildMockOfClass(Participant::class));
        
        $clientParticipant = new ClientParticipant($this->client, $this->clientParticipantId, $participant);
        $this->assertEquals($clientParticipant, $this->addAsProgramApplicant());
    }
    public function test_addAsProgramApplicant_clientIsActiveParticipantOrRegistrantOfSameProgram_forbidden()
    {
        $this->clientParticipant->expects($this->once())
                ->method('isActiveParticipantOrRegistrantOfProgram')
                ->with($this->program)
                ->willReturn(true);
        $this->assertRegularExceptionThrowed(function () {
            $this->addAsProgramApplicant();
        }, 'Forbidden', 'unable to process program application, reason: already active registrant or participant of same program');
    }
    
    protected function addAsActiveProgramParticipant()
    {
        return $this->client->addAsActiveProgramParticipant($this->clientParticipantId, $this->program);
    }
    public function test_addAsActiveProgramParticipant_returnClientParticipant()
    {
        $this->program->expects($this->once())
                ->method('createActiveParticipant')
                ->with($this->clientParticipantId, 'client')
                ->willReturn($this->participant);
        $clientParticipant = new ClientParticipant($this->client, $this->clientParticipantId, $this->participant);
        $this->assertEquals($clientParticipant, $this->addAsActiveProgramParticipant());
    }
    public function test_addAsActiveProgramParticipant_clientIsActiveParticipantOrRegistrantOfSameProgram_forbidden()
    {
        $this->clientParticipant->expects($this->once())
                ->method('isActiveParticipantOrRegistrantOfProgram')
                ->with($this->program)
                ->willReturn(true);
        $this->assertRegularExceptionThrowed(function () {
            $this->addAsActiveProgramParticipant();
        }, 'Forbidden', 'unable to add as active participant, already active registrant or participant of same program');
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
}
