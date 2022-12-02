<?php

namespace SharedContext\Domain\ValueObject;

use Tests\TestBase;

class TaskReportReviewStatusTest extends TestBase
{
    protected $taskReportReviewStatus;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->taskReportReviewStatus = new TestableTaskReportReviewStatus();
    }
    
    //
    protected function construct()
    {
      return new TestableTaskReportReviewStatus();  
    }
    public function test_construct_setValue()
    {
        $reviewStatus = $this->construct();
        $this->assertSame(TaskReportReviewStatus::UNREVIEWED, $reviewStatus->value);
    }
    
    //
    protected function approve()
    {
        return $this->taskReportReviewStatus->approve();
    }
    public function test_approve_returnNewReviewStatusViewApprovedValue()
    {
        $reviewStatus = $this->approve();
        $this->assertSame(TaskReportReviewStatus::APPROVED, $reviewStatus->value);
    }
    
    //
    protected function askForRevision()
    {
        return $this->taskReportReviewStatus->askForRevision();
    }
    public function test_askForRevision_returnNewReviewStatusWithRevisionRequiredValue()
    {
        $reviewStatus = $this->askForRevision();
        $this->assertSame(TaskReportReviewStatus::REVISION_REQUIRED, $reviewStatus->value);
    }
    public function test_askForRevision_allreadyApproved_forbidden()
    {
        $this->taskReportReviewStatus->value = TaskReportReviewStatus::APPROVED;
        $this->assertRegularExceptionThrowed(function () {
            $this->askForRevision();
        }, 'Forbidden', 'report already approved, unable to make further change');
    }
    
    //
    protected function revise()
    {
        return $this->taskReportReviewStatus->revise();
    }
    public function test_revise_returnNewStatusWithUnreviewedStatus()
    {
        $reviewStatus = $this->revise();
        $this->assertSame(TaskReportReviewStatus::UNREVIEWED, $reviewStatus->value);
    }
    public function test_revise_alreadyApproved_forbidden()
    {
        $this->taskReportReviewStatus->value = TaskReportReviewStatus::APPROVED;
        $this->assertRegularExceptionThrowed(function () {
            $this->revise();
        }, 'Forbidden', 'report already approved, unable to make further change');
    }
}

class TestableTaskReportReviewStatus extends TaskReportReviewStatus
{
    public $value;
}
