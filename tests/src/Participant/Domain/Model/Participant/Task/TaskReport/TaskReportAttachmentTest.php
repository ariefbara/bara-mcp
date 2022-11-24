<?php

use Participant\Domain\Model\Participant\ParticipantFileInfo;
use Participant\Domain\Model\Participant\Task\TaskReport;
use Participant\Domain\Model\Participant\Task\TaskReport\TaskReportAttachment;
use Participant\Domain\Model\Participant\Task\TaskReportData;
use Tests\TestBase;

class TaskReportAttachmentTest extends TestBase
{
    protected $taskReport;
    protected $participantFileInfo;
    protected $taskReportAttachment;
    //
    protected $id = 'newId';
    //
    protected $taskReportData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->taskReport = $this->buildMockOfClass(TaskReport::class);
        $this->participantFileInfo = $this->buildMockOfClass(ParticipantFileInfo::class);
        
        $this->taskReportAttachment = new TestableTaskReportAttachment($this->taskReport, 'id', $this->participantFileInfo);
        //
        $this->taskReportData = $this->buildMockOfClass(TaskReportData::class);
    }
    
    protected function construct()
    {
        return new TestableTaskReportAttachment($this->taskReport, $this->id, $this->participantFileInfo);
    }
    public function test_construct_setProperties()
    {
        $attachment = $this->construct();
        $this->assertSame($this->taskReport, $attachment->taskReport);
        $this->assertSame($this->id, $attachment->id);
        $this->assertSame($this->participantFileInfo, $attachment->participantFileInfo);
        $this->assertFalse($attachment->removed);
    }
    
    //
    protected function update()
    {
        return $this->taskReportAttachment->update($this->taskReportData);
    }
    public function test_update_dataContainDetachableParticipantFileINfo_returnFalse()
    {
        $this->taskReportData->expects($this->once())
                ->method('detachParticipantFileInfo')
                ->with($this->participantFileInfo)
                ->willReturn(true);
        $this->assertFalse($this->update());
    }
    public function test_update_noDetachableParticipantFileInfoInData_setRemovedAndReturnFalse()
    {
        $this->taskReportData->expects($this->once())
                ->method('detachParticipantFileInfo')
                ->with($this->participantFileInfo)
                ->willReturn(false);
        $this->assertTrue($this->update());
        $this->assertTrue($this->taskReportAttachment->removed);
    }
}

class TestableTaskReportAttachment extends TaskReportAttachment
{
    public $taskReport;
    public $id;
    public $participantFileInfo;
    public $removed;
}
