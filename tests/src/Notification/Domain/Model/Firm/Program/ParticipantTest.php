<?php

namespace Notification\Domain\Model\Firm\Program;

use Notification\Domain\Model\ {
    Firm\Client\ClientParticipant,
    Firm\Program,
    User\UserParticipant
};
use Query\Domain\Model\FirmWhitelableInfo;
use Tests\TestBase;

class ParticipantTest extends TestBase
{
    protected $participant;
    protected $program;
    protected $clientParticipant;
    protected $userParticipant;
    
    protected $firmWhitelableInfo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = new TestableParticipant();
        
        $this->program = $this->buildMockOfClass(Program::class);
        $this->participant->program = $this->program;
        
        $this->clientParticipant = $this->buildMockOfClass(ClientParticipant::class);
        $this->participant->clientParticipant = $this->clientParticipant;
        
        $this->userParticipant = $this->buildMockOfClass(UserParticipant::class);
        $this->participant->userParticipant = $this->userParticipant;
        
        $this->firmWhitelableInfo = $this->buildMockOfClass(FirmWhitelableInfo::class);
    }
    
    public function test_qetFirmWhitelableInfo_returnProgramsGetProgramFirmWhitelableInfo()
    {
        $this->program->expects($this->once())
                ->method('getFirmWhitelableInfo');
        $this->participant->getFirmWhitelableInfo();
    }
    
    protected function executeGetName()
    {
        return $this->participant->getName();
    }
    public function test_getName_returnClientParticipantsGetClientNameResult()
    {
        $this->clientParticipant->expects($this->once())
                ->method('getClientName')
                ->willReturn($clientName = 'client name');
        $this->assertEquals($clientName, $this->participant->getName());
    }
    public function test_getName_emptyClientParticipant_returnUserParticipantGetUserNameResult()
    {
        $this->participant->clientParticipant = null;
        $this->userParticipant->expects($this->once())
                ->method('getUserName')
                ->willReturn($name = 'username');
        $this->assertEquals($name, $this->participant->getName());
    }
    
    public function test_getProgramId_returnClientParticipantsGetProgramIdResult()
    {
        $this->program->expects($this->once())
                ->method('getId');
        $this->participant->getProgramId();
    }
    
}

class TestableParticipant extends Participant
{
    public $program;
    public $id;
    public $active = true;
    public $clientParticipant;
    public $userParticipant;
    
    function __construct()
    {
        parent::__construct();
    }
}
