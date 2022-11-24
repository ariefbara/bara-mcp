<?php

namespace Participant\Domain\Model\Participant\Task;

use Participant\Domain\Model\Participant\ParticipantFileInfo;
use Tests\TestBase;

class TaskReportDataTest extends TestBase
{
    protected $taskReportData;
    //
    protected $participantFileInfoOne;
    protected $participantFileInfoTwo;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->taskReportData = new TestableTaskReportData('content');
        
        $this->participantFileInfoOne = $this->buildMockOfClass(ParticipantFileInfo::class);
        $this->taskReportData->attachParticipantFileInfo($this->participantFileInfoOne);
        
        $this->participantFileInfoTwo = $this->buildMockOfClass(ParticipantFileInfo::class);
    }
    
    protected function detachParticipantFileInfo()
    {
        return $this->taskReportData->detachParticipantFileInfo($this->participantFileInfoOne);
    }
    public function test_detachParticipantFileInfo_storageHasCorrespondingFileInfo_detachFromStorageAndReturnTrue()
    {
        $this->assertTrue($this->detachParticipantFileInfo());
        $this->assertFalse($this->taskReportData->attachedParticipantFileInfoList->contains($this->participantFileInfoOne));
    }
    public function test_detachParticipantFileInfo_noCorrespondingFileInfo_returnFalse()
    {
        $this->assertFalse($this->taskReportData->detachParticipantFileInfo($this->participantFileInfoTwo));
        $this->assertTrue($this->taskReportData->attachedParticipantFileInfoList->contains($this->participantFileInfoOne));
    }
}

class TestableTaskReportData extends TaskReportData
{
    public $content;
    public $attachedParticipantFileInfoList;
}
