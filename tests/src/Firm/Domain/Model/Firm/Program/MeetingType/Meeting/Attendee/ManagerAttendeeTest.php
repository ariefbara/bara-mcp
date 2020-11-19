<?php

namespace Firm\Domain\Model\Firm\Program\MeetingType\Meeting\Attendee;

use Firm\Domain\Model\Firm\ {
    Manager,
    Program\MeetingType\CanAttendMeeting,
    Program\MeetingType\Meeting\Attendee
};
use Tests\TestBase;

class ManagerAttendeeTest extends TestBase
{
    protected $attendee;
    protected $manager;
    protected $managerAttendee;
    protected $id = "newId";
    protected $user;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->attendee = $this->buildMockOfClass(Attendee::class);
        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->managerAttendee = new TestableManagerAttendee($this->attendee, "id", $this->manager);
    }
    
    public function test_construct_setProperties()
    {
        $managerAttendee = new TestableManagerAttendee($this->attendee, $this->id, $this->manager);
        $this->assertEquals($this->attendee, $managerAttendee->attendee);
        $this->assertEquals($this->id, $managerAttendee->id);
        $this->assertEquals($this->manager, $managerAttendee->manager);
    }
    
    public function test_managerEquals_userIsSameManager_returnTrue()
    {
        $this->assertTrue($this->managerAttendee->managerEquals($this->managerAttendee->manager));
    }
    public function test_managerEquals_userIsDifferentManager_returnFalse()
    {
        $user = $this->buildMockOfInterface(CanAttendMeeting::class);
        $this->assertFalse($this->managerAttendee->managerEquals($user));
    }
}

class TestableManagerAttendee extends ManagerAttendee
{
    public $attendee;
    public $id;
    public $manager;
}

