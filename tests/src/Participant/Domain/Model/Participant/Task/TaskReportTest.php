<?php

namespace Participant\Domain\Model\Participant\Task;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\Model\Participant\ParticipantFileInfo;
use Participant\Domain\Model\Participant\Task;
use Participant\Domain\Model\Participant\Task\TaskReport\TaskReportAttachment;
use Resources\DateTimeImmutableBuilder;
use SharedContext\Domain\ValueObject\TaskReportReviewStatus;
use Tests\TestBase;

class TaskReportTest extends TestBase
{
    protected $task;
    protected $taskReport, $reviewStatus, $attachmentOne;
    //
    protected $id = 'newId';
    protected $content = 'new content', $participantFileInfoOne, $participantFileInfoTwo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->task = $this->buildMockOfClass(Task::class);
        
        
        $this->taskReport = new TestableTaskReport($this->task, 'id', new TaskReportData('content'));
        $this->reviewStatus = $this->buildMockOfClass(TaskReportReviewStatus::class);
        $this->taskReport->reviewStatus = $this->reviewStatus;
        $this->taskReport->attachments = new ArrayCollection();
        $this->taskReport->modifiedTime = new DateTimeImmutable('-7 days');
        
        $this->attachmentOne = $this->buildMockOfClass(TaskReportAttachment::class);
        $this->taskReport->attachments->add($this->attachmentOne);
        //
        $this->participantFileInfoOne = $this->buildMockOfClass(ParticipantFileInfo::class);
        $this->participantFileInfoTwo = $this->buildMockOfClass(ParticipantFileInfo::class);
    }
    //
    protected function composeData()
    {
        $data = new TaskReportData($this->content);
        $data->attachParticipantFileInfo($this->participantFileInfoOne);
        $data->attachParticipantFileInfo($this->participantFileInfoTwo);
        return $data;
    }
    
    //
    protected function construct()
    {
        return new TestableTaskReport($this->task, $this->id, $this->composeData());
    }
    public function test_construct_setProperties()
    {
        $taskReport = $this->construct();
        $this->assertSame($this->task, $taskReport->task);
        $this->assertSame($this->id, $taskReport->id);
        $this->assertSame($this->content, $taskReport->content);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $taskReport->createdTime);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $taskReport->modifiedTime);
        $this->assertEquals(new TaskReportReviewStatus(), $taskReport->reviewStatus);
        $this->assertInstanceOf(ArrayCollection::class, $taskReport->attachments);
    }
    public function test_construct_setAttachments()
    {
        $taskReport = $this->construct();
        $this->assertEquals(2, $taskReport->attachments->count());
        $this->assertInstanceOf(TaskReportAttachment::class, $taskReport->attachments->first());
        $this->assertInstanceOf(TaskReportAttachment::class, $taskReport->attachments->last());
    }
    
    //
    protected function update()
    {
        $this->attachmentOne->expects($this->any())
                ->method('isRemoved')
                ->willReturn(false);
        
        $this->taskReport->update($this->composeData());
    }
    public function test_update_updateContentModifiedTimeAndReviseReviewStatus()
    {
        $updatedReviewStatus = $this->buildMockOfClass(TaskReportReviewStatus::class);
        $this->reviewStatus->expects($this->once())
                ->method('revise')
                ->willReturn($updatedReviewStatus);
        $this->update();
        $this->assertSame($this->content, $this->taskReport->content);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $this->taskReport->modifiedTime);
        $this->assertSame($updatedReviewStatus, $this->taskReport->reviewStatus);
    }
    public function test_update_updateExistingAttachment()
    {
        $this->attachmentOne->expects($this->once())
                ->method('update')
                ->with($this->composeData());
        $this->update();
    }
    public function test_update_containRemovedAttachment_ignore()
    {
        $this->attachmentOne->expects($this->once())
                ->method('isRemoved')
                ->willReturn(true);
        
        $this->attachmentOne->expects($this->never())
                ->method('update');
        $this->update();
    }
    public function test_update_attachNewFiles()
    {
        $this->update();
        $this->assertEquals(3, $this->taskReport->attachments->count());
        $this->assertInstanceOf(TaskReportAttachment::class, $this->taskReport->attachments->last());
    }
    public function test_update_contentChange_updateModifiedTime()
    {
        $this->update();
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $this->taskReport->modifiedTime);
    }
    public function test_update_noChangeInContentAndAttachment_dontUpdateModifiedTimeAndReviewStatus()
    {
        $data = new TaskReportData($this->taskReport->content);
        
        $this->attachmentOne->expects($this->any())
                ->method('isRemoved')
                ->willReturn(false);
        
        $this->attachmentOne->expects($this->once())
                ->method('update')
                ->with($data)
                ->willReturn(false);
        
        $previousModifiedTime = $this->taskReport->modifiedTime;
        $this->taskReport->update($data);
        $this->assertEquals($previousModifiedTime, $this->taskReport->modifiedTime);
        $this->assertSame($this->reviewStatus, $this->taskReport->reviewStatus);
    }
    public function test_update_existingAttachmentUpdated_updateModifiedTime()
    {
        $data = new TaskReportData($this->taskReport->content);
        
        $this->attachmentOne->expects($this->any())
                ->method('isRemoved')
                ->willReturn(false);
        $this->attachmentOne->expects($this->once())
                ->method('update')
                ->with($data)
                ->willReturn(true);
        
        $this->taskReport->update($data);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $this->taskReport->modifiedTime);
    }
    public function test_update_newAttachment_updateModifiedTime()
    {
        $data = new TaskReportData($this->taskReport->content);
        $data->attachParticipantFileInfo($this->participantFileInfoOne);
        
        $this->attachmentOne->expects($this->any())
                ->method('isRemoved')
                ->willReturn(false);
        $this->attachmentOne->expects($this->once())
                ->method('update')
                ->with($data)
                ->willReturn(false);
        
        $this->taskReport->update($data);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $this->taskReport->modifiedTime);
    }
}

class TestableTaskReport extends TaskReport
{
    public $task;
    public $id;
    public $content;
    public $reviewStatus;
    public $createdTime;
    public $modifiedTime;
    public $attachments;
}
