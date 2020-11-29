<?php

namespace Firm\Domain\Model\Firm\Program\MeetingType\Meeting\Attendee;

use Firm\Domain\Model\Firm\Program\{
    Consultant,
    MeetingType\CanAttendMeeting,
    MeetingType\Meeting\Attendee
};
use Tests\TestBase;

class ConsultantAttendeeTest extends TestBase
{

    protected $attendee;
    protected $consultant;
    protected $consultantAttendee;
    protected $id = "newId";
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->attendee = $this->buildMockOfClass(Attendee::class);
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->consultantAttendee = new TestableConsultantAttendee($this->attendee, "id", $this->consultant);
    }

    public function test_construct_setProperties()
    {
        $consultantAttendee = new TestableConsultantAttendee($this->attendee, $this->id, $this->consultant);
        $this->assertEquals($this->attendee, $consultantAttendee->attendee);
        $this->assertEquals($this->id, $consultantAttendee->id);
        $this->assertEquals($this->consultant, $consultantAttendee->consultant);
    }

    public function test_consultantEquals_userIsSameConsultant_returnTrue()
    {
        $this->assertTrue($this->consultantAttendee->consultantEquals($this->consultantAttendee->consultant));
    }

    public function test_consultantEquals_userIsDifferentConsultant_returnFalse()
    {
        $user = $this->buildMockOfInterface(CanAttendMeeting::class);
        $this->assertFalse($this->consultantAttendee->consultantEquals($user));
    }
    
    public function test_disableValidInvitation_executeAttendeesDisableValidInvitation()
    {
        $this->attendee->expects($this->once())
                ->method("disableValidInvitation");
        $this->consultantAttendee->disableValidInvitation();
    }

}

class TestableConsultantAttendee extends ConsultantAttendee
{

    public $attendee;
    public $id;
    public $consultant;

}
