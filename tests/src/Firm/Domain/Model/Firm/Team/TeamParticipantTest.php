<?php

namespace Firm\Domain\Model\Firm\Team;

use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\Participant;
use Firm\Domain\Model\Firm\Team;
use Tests\TestBase;

class TeamParticipantTest extends TestBase
{
    protected $team;
    protected $participant;
    protected $teamParticipant;
    protected $id = 'newId';
    //
    protected $program;

    protected function setUp(): void
    {
        parent::setUp();
        $this->team = $this->buildMockOfClass(Team::class);
        $this->participant = $this->buildMockOfClass(Participant::class);
        //
        $this->teamParticipant = new TestableTeamParticipant($this->team, 'id', $this->participant);
        //
        $this->program = $this->buildMockOfClass(Program::class);
    }
    
    protected function construct()
    {
        return new TestableTeamParticipant($this->team, $this->id, $this->participant);
    }
    public function test_construct_setProperties()
    {
        $teamParticipant = $this->construct();
        $this->assertSame($this->team, $teamParticipant->team);
        $this->assertSame($this->id, $teamParticipant->id);
        $this->assertSame($this->participant, $teamParticipant->participant);
    }
    
    protected function pullRecordedEvents()
    {
        return $this->teamParticipant->pullRecordedEvents();
    }
    public function test_pullRecordedEvents_returnEventPulledFromParticipant()
    {
        $this->participant->expects($this->once())
                ->method('pullRecordedEvents')
                ->willReturn($events = ['array represent event in participant']);
        $this->assertSame($events, $this->pullRecordedEvents());
    }
    
    protected function isActiveParticipantOrRegistrantOfProgram()
    {
        return $this->teamParticipant->isActiveParticipantOrRegistrantOfProgram($this->program);
    }
    public function test_isActiveParticipantOrRegistrantOfProgram_returnComparisonResultInParticipant()
    {
        $this->participant->expects($this->once())
                ->method('isActiveParticipantOrRegistrantOfProgram')
                ->with($this->program);
        $this->isActiveParticipantOrRegistrantOfProgram();
    }
}

class TestableTeamParticipant extends TeamParticipant
{
    public $team;
    public $id;
    public $participant;
}
