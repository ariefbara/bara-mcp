<?php

namespace ActivityCreator\Domain\Model\Activity;

use ActivityCreator\Domain\{
    DependencyModel\Firm\Program\Participant,
    service\ActivityDataProvider
};
use Tests\TestBase;

class ParticipantInvitationTest extends TestBase
{

    protected $invitation;
    protected $participant;
    protected $participantInvitation;
    protected $id = 'newId';
    protected $activityDataProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invitation = $this->buildMockOfClass(Invitation::class);
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->participantInvitation = new TestableParticipantInvitation($this->invitation, "id", $this->participant);

        $this->activityDataProvider = $this->buildMockOfClass(ActivityDataProvider::class);
    }

    public function test_construct_setProperties()
    {
        $participantInvitation = new TestableParticipantInvitation($this->invitation, $this->id, $this->participant);
        $this->assertEquals($this->invitation, $participantInvitation->invitation);
        $this->assertEquals($this->id, $participantInvitation->id);
        $this->assertEquals($this->participant, $participantInvitation->participant);
    }
    
    protected function executeRemoveIfNotAppearInList()
    {
        $this->participantInvitation->removeIfNotApprearInList($this->activityDataProvider);
    }
    
    public function test_removeIfNotAppearInList_removeInvitation()
    {
        $this->invitation->expects($this->once())
                ->method("remove");
        $this->executeRemoveIfNotAppearInList();
    }
    public function test_removeIfNotAppearInList_appearInList_preventRemovingInvitation()
    {
        $this->activityDataProvider->expects($this->once())
                ->method("containParticipant")
                ->with($this->participant)
                ->willReturn(true);
        $this->invitation->expects($this->never())
                ->method("remove");
        $this->executeRemoveIfNotAppearInList();
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

class TestableParticipantInvitation extends ParticipantInvitation
{

    public $invitation;
    public $id;
    public $participant;

}
