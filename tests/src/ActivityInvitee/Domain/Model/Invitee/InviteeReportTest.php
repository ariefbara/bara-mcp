<?php

namespace ActivityInvitee\Domain\Model\Invitee;

use ActivityInvitee\Domain\Model\Invitee;
use SharedContext\Domain\Model\SharedEntity\ {
    FormRecord,
    FormRecordData
};
use Tests\TestBase;

class InviteeReportTest extends TestBase
{
    protected $invitee;
    protected $formRecord;
    protected $inviteeReport;
    protected $id = "newId";
    protected $formRecordData;


    protected function setUp(): void
    {
        parent::setUp();
        $this->invitee = $this->buildMockOfClass(Invitee::class);
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
        
        $this->inviteeReport = new TestableInviteeReport($this->invitee, "id", $this->formRecord);
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    public function test_construct_setProperties()
    {
        $inviteeReport = new TestableInviteeReport($this->invitee, $this->id, $this->formRecord);
        $this->assertEquals($this->invitee, $inviteeReport->invitee);
        $this->assertEquals($this->id, $inviteeReport->id);
        $this->assertEquals($this->formRecord, $inviteeReport->formRecord);
    }
    
    public function test_update_updateFormRecord()
    {
        $this->formRecord->expects($this->once())
                ->method("update")
                ->with($this->formRecordData);
        $this->inviteeReport->update($this->formRecordData);
    }
}

class TestableInviteeReport extends InviteeReport
{
    public $invitee;
    public $id;
    public $formRecord;
}
