<?php

namespace Participant\Domain\Model\Participant;

use Participant\Domain\Model\Participant;
use Participant\Domain\Model\Participant\Task\TaskReport;
use Tests\TestBase;

class TaskTest extends TestBase
{
    protected $task, $participant, $taskReport;
    //
    protected $taskReportData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->task = new TestableTask();
        
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->task->participant = $this->participant;
        
        $this->taskReport = $this->buildMockOfClass(TaskReport::class);
        //
        $this->taskReportData = new Task\TaskReportData('content');
    }
    
    //
    protected function submitReport()
    {
        $this->task->submitReport($this->taskReportData);
    }
    public function test_submitReport_addTaskReport()
    {
        $this->submitReport();
        $this->assertInstanceOf(TaskReport::class, $this->task->taskReport);
    }
    public function test_submitReport_alreadyContainReport_updateReport()
    {
        $this->task->taskReport = $this->taskReport;
        $this->taskReport->expects($this->once())
                ->method('update')
                ->with($this->taskReportData);
        $this->submitReport();
    }
    public function test_submitReport_preventOVerrideExistingReport()
    {
        $this->task->taskReport = $this->taskReport;
        $this->submitReport();
        $this->assertSame($this->taskReport, $this->task->taskReport);
    }
    
    //
    protected function assertManageableByParticipant()
    {
        $this->task->assertManageableByParticipant($this->participant);
    }
    public function test_assertManageableByParticipant_differentParticipant_forbidden()
    {
        $this->task->participant = $this->buildMockOfClass(Participant::class);
        $this->assertRegularExceptionThrowed(function () {
            $this->assertManageableByParticipant();
        }, 'Forbidden', 'unmanaged task, can only manage active own task');
    }
    public function test_assertManageableByParticipant_cancelledTask_forbidden()
    {
        $this->task->cancelled = true;
        $this->assertRegularExceptionThrowed(function () {
            $this->assertManageableByParticipant();
        }, 'Forbidden', 'unmanaged task, can only manage active own task');
    }
    public function test_assertManageablyByParticipant_sameParticipant_void()
    {
        $this->assertManageableByParticipant();
        $this->markAsSuccess();
    }
}

class TestableTask extends Task
{
    public $participant;
    public $id = 'taskId';
    public $cancelled = false;
    public $taskReport;
    
    function __construct()
    {
        parent::__construct();
    }
}
