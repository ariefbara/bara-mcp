<?php

namespace ActivityCreator\Domain\Model\Activity\Invitee;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Personnel\Consultant,
    Model\Activity\Invitee
};
use Tests\TestBase;

class ConsultantInviteeTest extends TestBase
{
    protected $invitee;
    protected $consultant;
    protected $consultantInvitation;
    protected $id = "newId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->invitee = $this->buildMockOfClass(Invitee::class);
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->consultantInvitation = new TestableConsultantInvitation($this->invitee, "id", $this->consultant);
    }
    
    public function test_construct_setProperties()
    {
        $consultantInvitation = new TestableConsultantInvitation($this->invitee, $this->id, $this->consultant);
        $this->assertEquals($this->invitee, $consultantInvitation->invitee);
        $this->assertEquals($this->id, $consultantInvitation->id);
        $this->assertEquals($this->consultant, $consultantInvitation->consultant);
    }
    
    public function test_consultantEquals_sameConsultant_returnTrue()
    {
        $this->assertTrue($this->consultantInvitation->consultantEquals($this->consultantInvitation->consultant));
    }
    public function test_consultantEquals_differentConsultant_returnFalse()
    {
        $consultant = $this->buildMockOfClass(Consultant::class);
        $this->assertFalse($this->consultantInvitation->consultantEquals($consultant));
    }
}

class TestableConsultantInvitation extends ConsultantInvitee
{
    public $invitee;
    public $id;
    public $consultant;
}

