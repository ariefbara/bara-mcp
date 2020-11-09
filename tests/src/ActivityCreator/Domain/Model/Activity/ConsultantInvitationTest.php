<?php

namespace ActivityCreator\Domain\Model\Activity;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Personnel\Consultant,
    service\ActivityDataProvider
};
use Tests\TestBase;

class ConsultantInvitationTest extends TestBase
{
    protected $invitation;
    protected $consultant;
    protected $consultantInvitation;
    protected $id = 'newId';
    protected $activityDataProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invitation = $this->buildMockOfClass(Invitation::class);
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->consultantInvitation = new TestableConsultantInvitation($this->invitation, "id", $this->consultant);

        $this->activityDataProvider = $this->buildMockOfClass(ActivityDataProvider::class);
    }

    public function test_construct_setProperties()
    {
        $consultantInvitation = new TestableConsultantInvitation($this->invitation, $this->id, $this->consultant);
        $this->assertEquals($this->invitation, $consultantInvitation->invitation);
        $this->assertEquals($this->id, $consultantInvitation->id);
        $this->assertEquals($this->consultant, $consultantInvitation->consultant);
    }
    
    protected function executeRemoveIfNotAppearInList()
    {
        $this->consultantInvitation->removeIfNotApprearInList($this->activityDataProvider);
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
                ->method("containConsultant")
                ->with($this->consultant)
                ->willReturn(true);
        $this->invitation->expects($this->never())
                ->method("remove");
        $this->executeRemoveIfNotAppearInList();
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

class TestableConsultantInvitation extends ConsultantInvitation
{
    public $invitation;
    public $id;
    public $consultant;
}
