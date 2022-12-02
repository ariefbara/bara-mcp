<?php

namespace Personnel\Domain\Model\Firm\Program\Participant\Task;

use SharedContext\Domain\ValueObject\TaskReportReviewStatus;
use Tests\TestBase;

class TaskReportTest extends TestBase
{
    protected $taskReport, $reviewStatus;
    //
    protected $updatedReviewStatus;

    protected function setUp(): void
    {
        parent::setUp();
        $this->taskReport = new TestableTaskReport();
        
        $this->reviewStatus = $this->buildMockOfClass(TaskReportReviewStatus::class);
        $this->taskReport->reviewStatus = $this->reviewStatus;
        
        $this->updatedReviewStatus = $this->buildMockOfClass(TaskReportReviewStatus::class);
    }
    
    //
    protected function approve()
    {
        $this->taskReport->approve();
    }
    public function test_approve_approveReviewStatus()
    {
        $this->reviewStatus->expects($this->once())
                ->method('approve')
                ->willReturn($this->updatedReviewStatus);
        $this->approve();
        $this->assertSame($this->updatedReviewStatus, $this->taskReport->reviewStatus);
    }
    
    //
    protected function askForRevision()
    {
        $this->taskReport->askForRevision();
    }
    public function test_askForRevision_updateReviewStausToAskForRevision()
    {
        $this->reviewStatus->expects($this->once())
                ->method('askForRevision')
                ->willReturn($this->updatedReviewStatus);
        $this->askForRevision();
        $this->assertSame($this->updatedReviewStatus, $this->taskReport->reviewStatus);
    }
}

class TestableTaskReport extends TaskReport
{

    public $task;
    public $id;
    public $reviewStatus;

    public function __construct()
    {
        parent::__construct();
    }

}
