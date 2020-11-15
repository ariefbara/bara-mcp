<?php

namespace ActivityInvitee\Domain\Model;

use ActivityInvitee\Domain\DependencyModel\Firm\Program\Participant;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class ParticipantInviteeTest extends TestBase
{
    protected $participantInvitee;
    protected $participant;
    protected $invitee;
    protected $formRecordData;


    protected function setUp(): void
    {
        parent::setUp();
        $this->participantInvitee = new TestableParticipantInvitee();
        $this->invitee = $this->buildMockOfClass(Invitee::class);
        $this->participantInvitee->invitee = $this->invitee;
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->participantInvitee->participant = $this->participant;
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    public function test_submitReport_executeInviteesSubmitReport()
    {
        $this->invitee->expects($this->once())
                ->method("submitReport")
                ->with($this->formRecordData);
        $this->participantInvitee->submitReport($this->formRecordData);
    }
    
    public function test_belongsToTeam_returnParticipantBelongsToTeamResult()
    {
        $this->participant->expects($this->once())
                ->method("belongsToTeam")
                ->with($teamId = "teamId");
        $this->participantInvitee->belongsToTeam($teamId);
    }
}

class TestableParticipantInvitee extends ParticipantInvitee
{
    public $participant;
    public $id;
    public $invitee;
    
    function __construct()
    {
        parent::__construct();
    }
}
