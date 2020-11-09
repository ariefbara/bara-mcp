<?php

namespace ActivityCreator\Domain\Model\Activity;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Manager,
    service\ActivityDataProvider
};
use Tests\TestBase;

class ManagerInvitationTest extends TestBase
{
    protected $invitation;
    protected $manager;
    protected $managerInvitation;
    protected $id = 'newId';
    protected $activityDataProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invitation = $this->buildMockOfClass(Invitation::class);
        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->managerInvitation = new TestableManagerInvitation($this->invitation, "id", $this->manager);

        $this->activityDataProvider = $this->buildMockOfClass(ActivityDataProvider::class);
    }

    public function test_construct_setProperties()
    {
        $managerInvitation = new TestableManagerInvitation($this->invitation, $this->id, $this->manager);
        $this->assertEquals($this->invitation, $managerInvitation->invitation);
        $this->assertEquals($this->id, $managerInvitation->id);
        $this->assertEquals($this->manager, $managerInvitation->manager);
    }
    
    protected function executeRemoveIfNotAppearInList()
    {
        $this->managerInvitation->removeIfNotApprearInList($this->activityDataProvider);
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
                ->method("containManager")
                ->with($this->manager)
                ->willReturn(true);
        $this->invitation->expects($this->never())
                ->method("remove");
        $this->executeRemoveIfNotAppearInList();
    }
    
    public function test_managerEquals_sameManager_returnTrue()
    {
        $this->assertTrue($this->managerInvitation->managerEquals($this->managerInvitation->manager));
    }
    public function test_managerEquals_differentManager_returnFalse()
    {
        $manager = $this->buildMockOfClass(Manager::class);
        $this->assertFalse($this->managerInvitation->managerEquals($manager));
    }
}

class TestableManagerInvitation extends ManagerInvitation
{
    public $invitation;
    public $id;
    public $manager;
}
