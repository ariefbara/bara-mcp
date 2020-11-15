<?php

namespace ActivityCreator\Domain\Model\Activity\Invitee;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Program\Participant,
    Model\Activity\Invitee
};
use Tests\TestBase;

class ParticipantInviteeTest extends TestBase
{
    protected $invitee;
    protected $participant;
    protected $participantInvitation;
    protected $id = "newId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->invitee = $this->buildMockOfClass(Invitee::class);
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->participantInvitation = new TestableParticipantInvitation($this->invitee, "id", $this->participant);
    }
    
    public function test_construct_setProperties()
    {
        $participantInvitation = new TestableParticipantInvitation($this->invitee, $this->id, $this->participant);
        $this->assertEquals($this->invitee, $participantInvitation->invitee);
        $this->assertEquals($this->id, $participantInvitation->id);
        $this->assertEquals($this->participant, $participantInvitation->participant);
    }
    
    public function test_participantEquals_sameParticipant_returnTrue()
    {
        $this->assertTrue($this->participantInvitation->participantEquals($this->participantInvitation->participant));
    }
    public function test_participantEquals_differentParticipant_returnFalse()
    {
        $participant = $this->buildMockOfClass(Participant::class);
        $this->assertFalse($this->participantInvitation->participantEquals($participant));
    }
}

class TestableParticipantInvitation extends ParticipantInvitee
{
    public $invitee;
    public $id;
    public $participant;
}
