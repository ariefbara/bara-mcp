<?php

namespace Firm\Domain\Model\Firm\Program\MeetingType\Meeting\Attendee;

use Firm\Domain\Model\Firm\Program\ {
    Coordinator,
    MeetingType\CanAttendMeeting,
    MeetingType\Meeting\Attendee
};
use Tests\TestBase;

class CoordinatorAttendeeTest extends TestBase
{

    protected $attendee;
    protected $coordinator;
    protected $coordinatorAttendee;
    protected $id = "newId";
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->attendee = $this->buildMockOfClass(Attendee::class);
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->coordinatorAttendee = new TestableCoordinatorAttendee($this->attendee, "id", $this->coordinator);
    }

    public function test_construct_setProperties()
    {
        $coordinatorAttendee = new TestableCoordinatorAttendee($this->attendee, $this->id, $this->coordinator);
        $this->assertEquals($this->attendee, $coordinatorAttendee->attendee);
        $this->assertEquals($this->id, $coordinatorAttendee->id);
        $this->assertEquals($this->coordinator, $coordinatorAttendee->coordinator);
    }

    public function test_coordinatorEquals_userIsSameCoordinator_returnTrue()
    {
        $this->assertTrue($this->coordinatorAttendee->coordinatorEquals($this->coordinatorAttendee->coordinator));
    }

    public function test_coordinatorEquals_userIsDifferentCoordinator_returnFalse()
    {
        $user = $this->buildMockOfInterface(CanAttendMeeting::class);
        $this->assertFalse($this->coordinatorAttendee->coordinatorEquals($user));
    }
    
    public function test_disableValidInvitation_executeAttendeesDisableValidInvitation()
    {
        $this->attendee->expects($this->once())
                ->method("disableValidInvitation");
        $this->coordinatorAttendee->disableValidInvitation();
    }

}

class TestableCoordinatorAttendee extends CoordinatorAttendee
{

    public $attendee;
    public $id;
    public $coordinator;

}
