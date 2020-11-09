<?php

namespace ActivityCreator\Domain\Model\Activity;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Personnel\Coordinator,
    service\ActivityDataProvider
};
use Tests\TestBase;

class CoordinatorInvitationTest extends TestBase
{
    protected $invitation;
    protected $coordinator;
    protected $coordinatorInvitation;
    protected $id = 'newId';
    protected $activityDataProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invitation = $this->buildMockOfClass(Invitation::class);
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->coordinatorInvitation = new TestableCoordinatorInvitation($this->invitation, "id", $this->coordinator);

        $this->activityDataProvider = $this->buildMockOfClass(ActivityDataProvider::class);
    }

    public function test_construct_setProperties()
    {
        $coordinatorInvitation = new TestableCoordinatorInvitation($this->invitation, $this->id, $this->coordinator);
        $this->assertEquals($this->invitation, $coordinatorInvitation->invitation);
        $this->assertEquals($this->id, $coordinatorInvitation->id);
        $this->assertEquals($this->coordinator, $coordinatorInvitation->coordinator);
    }
    
    protected function executeRemoveIfNotAppearInList()
    {
        $this->coordinatorInvitation->removeIfNotApprearInList($this->activityDataProvider);
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
                ->method("containCoordinator")
                ->with($this->coordinator)
                ->willReturn(true);
        $this->invitation->expects($this->never())
                ->method("remove");
        $this->executeRemoveIfNotAppearInList();
    }
    
    public function test_coordinatorEquals_sameCoordinator_returnTrue()
    {
        $this->assertTrue($this->coordinatorInvitation->coordinatorEquals($this->coordinatorInvitation->coordinator));
    }
    public function test_coordinatorEquals_differentCoordinator_returnFalse()
    {
        $coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->assertFalse($this->coordinatorInvitation->coordinatorEquals($coordinator));
    }
}

class TestableCoordinatorInvitation extends CoordinatorInvitation
{
    public $invitation;
    public $id;
    public $coordinator;
}
