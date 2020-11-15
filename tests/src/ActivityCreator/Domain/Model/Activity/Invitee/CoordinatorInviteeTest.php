<?php

namespace ActivityCreator\Domain\Model\Activity\Invitee;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Personnel\Coordinator,
    Model\Activity\Invitee
};
use Tests\TestBase;

class CoordinatorInviteeTest extends TestBase
{
    protected $invitee;
    protected $coordinator;
    protected $coordinatorInvitation;
    protected $id = "newId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->invitee = $this->buildMockOfClass(Invitee::class);
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->coordinatorInvitation = new TestableCoordinatorInvitation($this->invitee, "id", $this->coordinator);
    }
    
    public function test_construct_setProperties()
    {
        $coordinatorInvitation = new TestableCoordinatorInvitation($this->invitee, $this->id, $this->coordinator);
        $this->assertEquals($this->invitee, $coordinatorInvitation->invitee);
        $this->assertEquals($this->id, $coordinatorInvitation->id);
        $this->assertEquals($this->coordinator, $coordinatorInvitation->coordinator);
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

class TestableCoordinatorInvitation extends CoordinatorInvitee
{
    public $invitee;
    public $id;
    public $coordinator;
}
