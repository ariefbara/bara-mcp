<?php

namespace Query\Domain\Model\Firm\Program\Participant;

use Query\Domain\Model\Firm\Client;
use Query\Domain\Model\Firm\Program\Consultant;
use Query\Domain\Model\Firm\Program\Participant;
use Tests\TestBase;

class DedicatedMentorTest extends TestBase
{
    protected $dedicatedMentor;
    protected $participant;
    protected $consultant, $personnelName = 'personnel name';
    
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dedicatedMentor = new TestableDedicatedMentor();
        
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->dedicatedMentor->participant = $this->participant;
        
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->consultant->expects($this->any())->method('getPersonnelName')->willReturn($this->personnelName);
        $this->dedicatedMentor->consultant = $this->consultant;
        
        $this->client = $this->buildMockOfClass(Client::class);
    }
    
    public function test_getMentorName_returnConsultantPersonnelName()
    {
        $this->consultant->expects($this->once())
                ->method('getPersonnelName');
        $this->dedicatedMentor->getMentorName();
    }
    
    protected function getMentorPlusTeamName()
    {
        $this->consultant->expects($this->any())
                ->method('getPersonnelName')
                ->willReturn($this->personnelName);
        return $this->dedicatedMentor->getMentorPlusTeamName();
    }
    public function test_getMentorPlusTeamName_returnMentorName()
    {
        $this->assertEquals($this->personnelName, $this->getMentorPlusTeamName());
    }
    public function test_getMentorPlusTeamName_teamParticipant_returnModifiedMentorName()
    {
        $this->participant->expects($this->once())
                ->method('getTeamName')
                ->willReturn($teamName = 'team name');
        $this->assertEquals("{$this->personnelName} (of team: {$teamName})", $this->getMentorPlusTeamName());
    }
    
    public function test_getListOfClientPlusTeamName_returnParticipantListOfClientPlusTeamName()
    {
        $this->participant->expects($this->once())
                ->method('getListOfClientPlusTeamName');
        $this->dedicatedMentor->getListOfClientPlusTeamName();
    }
    
    public function test_correspondWithClient_returnParticipantCorrespondWithClientStatusResult()
    {
        $this->participant->expects($this->once())
                ->method('correspondWithClient')
                ->with($this->client);
        $this->dedicatedMentor->correspondWithClient($this->client);
    }
    
    public function test_correspondWithParticipant_sameParticipant_returnTrue()
    {
        $this->assertTrue($this->dedicatedMentor->correspondWithParticipant($this->participant));
    }
    public function test_correspondWithParticipant_differentParticipant_returnTrue()
    {
        $this->dedicatedMentor->participant = $this->buildMockOfClass(Participant::class);
        $this->assertFalse($this->dedicatedMentor->correspondWithParticipant($this->participant));
    }
}

class TestableDedicatedMentor extends DedicatedMentor
{
    public $participant;
    public $id = 'dedicated-mentor-id';
    public $consultant;
    public $modifiedTime;
    public $cancelled = false;
    
    function __construct()
    {
        parent::__construct();
    }
}
