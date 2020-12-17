<?php

namespace Firm\Domain\Model\Firm\Program;

use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\Registrant\RegistrantProfile;
use Tests\TestBase;

class RegistrantTest extends TestBase
{

    protected $registrant;
    protected $profile;
    protected $program;
    protected $userRegistrant;
    protected $clientRegistrant;
    protected $teamRegistrant;

    protected $id = 'newRegistrantId';
    
    protected $participantId = 'participantId';
    
    protected $userId = 'userId', $clientId = 'clientId', $teamId = "clientId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->registrant = new TestableRegistrant();
        
        $this->profile = $this->buildMockOfClass(RegistrantProfile::class);
        $this->registrant->profiles = new ArrayCollection();
        $this->registrant->profiles->add($this->profile);
        
        $this->program = $this->buildMockOfClass(Program::class);
        $this->registrant->program = $this->program;
        
        $this->userRegistrant = $this->buildMockOfClass(UserRegistrant::class);
        $this->registrant->userRegistrant = $this->userRegistrant;
        
        $this->clientRegistrant = $this->buildMockOfClass(ClientRegistrant::class);
        
        $this->teamRegistrant = $this->buildMockOfClass(TeamRegistrant::class);
    }
    
    protected function executeAccept()
    {
        $this->registrant->accept();
    }

    public function test_accept_setConcludedTrueAndNoteAccepted()
    {
        $this->executeAccept();
        $this->assertTrue($this->registrant->concluded);
        $this->assertEquals('accepted', $this->registrant->note);
    }

    public function test_accept_alreadyConcluded_forbiddenError()
    {
        $this->registrant->concluded = true;
        $operation = function () {
            $this->executeAccept();
        };
        $errorDetail = "forbidden: application already concluded";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }

    protected function executeReject()
    {
        $this->registrant->reject();
    }

    public function test_reject_setConcludedFlagTrueAndNoteRejected()
    {
        $this->executeReject();
        $this->assertTrue($this->registrant->concluded);
        $this->assertEquals('rejected', $this->registrant->note);
    }
    public function test_reject_alreadyConcluded_throwEx()
    {
        $this->registrant->concluded = true;
        $operation = function () {
            $this->executeReject();
        };
        $errorDetail = "forbidden: application already concluded";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeCreateParticipant()
    {
        return $this->registrant->createParticipant($this->participantId);
    }
    public function test_createParticipant_aUserRegistrant_returnUserRegistrantCreateParticipantResult()
    {
        $this->userRegistrant->expects($this->once())
                ->method('createParticipant')
                ->with($this->program, $this->participantId);
        $this->executeCreateParticipant();
    }
    public function test_createParticipant_aClientRegistrant_returnClientRegistrantCreateParticipantResult()
    {
        $this->registrant->userRegistrant = null;
        $this->registrant->clientRegistrant = $this->clientRegistrant;
        
        $this->clientRegistrant->expects($this->once())
                ->method('createParticipant')
                ->with($this->program, $this->participantId);
        $this->executeCreateParticipant();
    }
    public function test_createParticipant_aTeamRegistrant_returnTeamRegistrantCreateParticipantResult()
    {
        $this->registrant->userRegistrant = null;
        $this->registrant->teamRegistrant = $this->teamRegistrant;
        
        $this->teamRegistrant->expects($this->once())
                ->method('createParticipant')
                ->with($this->program, $this->participantId);
        $this->executeCreateParticipant();
    }
    public function test_createParticipant_transferAllProfileToParticipant()
    {
        $this->profile->expects($this->once())
                ->method("transferToParticipant");
        $this->executeCreateParticipant();
    }
    public function test_createParticipant_preventTransferRemovedProfile()
    {
        $this->profile->expects($this->once())
                ->method("isRemoved")
                ->willReturn(true);
        $this->profile->expects($this->never())
                ->method("transferToParticipant");
        $this->executeCreateParticipant();
    }
    
    public function test_correspondWithUser_returnUserRegistrantUserIdEqualsResult()
    {
        $this->userRegistrant->expects($this->once())
                ->method('userIdEquals')
                ->with($this->userId);
        $this->registrant->correspondWithUser($this->userId);
    }
    public function test_correspondWithUser_emptyUserRegistrant_returnFalse()
    {
        $this->registrant->userRegistrant = null;
        $this->assertFalse($this->registrant->correspondWithUser($this->userId));
    }
    
    public function test_correspondWithClient_emptyClientRegistrant_returnFalse()
    {
        $this->assertFalse($this->registrant->correspondWithClient($this->clientId));
    }
    public function test_correspondWithClient_hasClientRegistrant_returnClientRegistrantsClientIdEqualsResult()
    {
        $this->registrant->clientRegistrant = $this->clientRegistrant;
        $this->clientRegistrant->expects($this->once())
                ->method('clientIdEquals')
                ->with($this->clientId);
        $this->registrant->correspondWithClient($this->clientId);
    }
    
    public function test_correspondWithTeam_emptyTeamRegistrant_returnFalse()
    {
        $this->assertFalse($this->registrant->correspondWithTeam($this->teamId));
    }
    public function test_correspondWithTeam_hasTeamRegistrant_returnTeamRegistrantsTeamIdEqualsResult()
    {
        $this->registrant->teamRegistrant = $this->teamRegistrant;
        $this->teamRegistrant->expects($this->once())
                ->method('teamIdEquals')
                ->with($this->teamId);
        $this->registrant->correspondWithTeam($this->teamId);
    }

}

class TestableRegistrant extends Registrant
{
    public $program;
    public $id;
    public $registeredTime;
    public $concluded = false;
    public $note = null;
    public $userRegistrant;
    public $clientRegistrant;
    public $teamRegistrant;
    public $profiles;
    
    function __construct()
    {
        parent::__construct();
    }
}
