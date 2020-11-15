<?php

namespace ActivityInvitee\Domain\Model;

use ActivityInvitee\Domain\ {
    DependencyModel\Firm\Program\ActivityType\ActivityParticipant,
    Model\Invitee\InviteeReport
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class InviteeTest extends TestBase
{
    protected $invitee;
    protected $activityParticipant;
    protected $formRecordData;
    protected $report;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invitee = new TestableInvitee();
        $this->activityParticipant = $this->buildMockOfClass(ActivityParticipant::class);
        $this->invitee->activityParticipant = $this->activityParticipant;
        
        $this->report = $this->buildMockOfClass(InviteeReport::class);
        $this->invitee->report = $this->report;
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    protected function executeSubmitReport()
    {
        $this->invitee->submitReport($this->formRecordData);
    }
    public function test_submitReport_addInviteeReportToRepository()
    {
        $this->invitee->report = null;
        
        $this->executeSubmitReport();
        $this->assertInstanceOf(InviteeReport::class, $this->invitee->report);
    }
    public function test_submitReport_alreayHasReport_updateExistingReport()
    {
        $this->report->expects($this->once())
                ->method("update")
                ->with($this->formRecordData);
        $this->executeSubmitReport();
    }
    public function test_submitReport_alreadyHasReport_preventAddingNewReport()
    {
        $this->executeSubmitReport();
        $this->assertEquals($this->report, $this->invitee->report);
    }
}

class TestableInvitee extends Invitee
{
    public $id;
    public $willAttend;
    public $activityParticipant;
    public $attended = true;
    public $invitationCancelled = false;
    public $report;
    
    function __construct()
    {
        parent::__construct();
    }
}
