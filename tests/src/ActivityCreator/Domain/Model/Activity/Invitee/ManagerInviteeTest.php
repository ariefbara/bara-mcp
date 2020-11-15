<?php

namespace ActivityCreator\Domain\Model\Activity\Invitee;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Manager,
    Model\Activity\Invitee
};
use Tests\TestBase;

class ManagerInviteeTest extends TestBase
{
    protected $invitee;
    protected $manager;
    protected $managerInvitation;
    protected $id = "newId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->invitee = $this->buildMockOfClass(Invitee::class);
        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->managerInvitation = new TestableManagerInvitation($this->invitee, "id", $this->manager);
    }
    
    public function test_construct_setProperties()
    {
        $managerInvitation = new TestableManagerInvitation($this->invitee, $this->id, $this->manager);
        $this->assertEquals($this->invitee, $managerInvitation->invitee);
        $this->assertEquals($this->id, $managerInvitation->id);
        $this->assertEquals($this->manager, $managerInvitation->manager);
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

class TestableManagerInvitation extends ManagerInvitee
{
    public $invitee;
    public $id;
    public $manager;
}
