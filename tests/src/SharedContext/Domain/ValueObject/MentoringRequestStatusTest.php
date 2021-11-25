<?php

namespace SharedContext\Domain\ValueObject;

use Tests\TestBase;

class MentoringRequestStatusTest extends TestBase
{
    protected $status;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->status = new TestableMentoringRequestStatus(MentoringRequestStatus::OFFERED);
    }
    
    protected function isConcluded()
    {
        return $this->status->isConcluded();
    }
    public function test_isConcluded_offeredStatus_returnFalse()
    {
        $this->assertFalse($this->isConcluded());
    }
    public function test_isConcluded_requestedStatus_returnFalse()
    {
        $this->status->value = MentoringRequestStatus::REQUESTED;
        $this->assertFalse($this->isConcluded());
    }
    public function test_isConcluded_nonRequestedOrOffered_returnTrue()
    {
        $this->status->value = MentoringRequestStatus::CANCELLED;
        $this->assertTrue($this->isConcluded());
    }
    
    protected function cancel()
    {
        return $this->status->cancel();
    }
    public function test_cancel_returnCancelledStatus()
    {
        $status = $this->cancel();
        $this->assertEquals(MentoringRequestStatus::CANCELLED, $status->value);
    }
    public function test_cancel_alreadyConcluded_forbidden()
    {
        $this->status->value = MentoringRequestStatus::ACCEPTED_BY_PARTICIPANT;
        $this->assertRegularExceptionThrowed(function() {
            $this->cancel();
        }, 'Forbidden', 'forbidden: unable to cancel concluded mentoring request');
    }
    
    protected function accept()
    {
        return $this->status->accept();
    }
    public function test_accept_scenario_expectedResult()
    {
        $status = $this->accept();
        $this->assertEquals(MentoringRequestStatus::ACCEPTED_BY_PARTICIPANT, $status->value);
    }
    public function test_accept_nonOfferedStatus_forbidden()
    {
        $this->status->value = MentoringRequestStatus::REQUESTED;
        $this->assertRegularExceptionThrowed(function() {
            $this->accept();
        }, 'Forbidden', 'forbidden: can only accept offered mentoring request');
    }
    
    protected function isScheduledOrPotentialSchedule()
    {
        return $this->status->isScheduledOrPotentialSchedule();
    }
    public function test_isScheduledOrPotentialSchedule_offeredStatus_returnFalse()
    {
        $this->assertFalse($this->isScheduledOrPotentialSchedule());
    }
    public function test_isScheduledOrPotentialSchedule_acceptedStatus_returnTrue()
    {
        $this->status->value = MentoringRequestStatus::ACCEPTED_BY_PARTICIPANT;
        $this->assertTrue($this->isScheduledOrPotentialSchedule());
    }
    public function test_isScheduledOrPotentialSchedule_approvedStatus_returnTrue()
    {
        $this->status->value = MentoringRequestStatus::APPROVED_BY_MENTOR;
        $this->assertTrue($this->isScheduledOrPotentialSchedule());
    }
    public function test_isScheduledOrPotentialSchedule_requestedStatus_returnTrue()
    {
        $this->status->value = MentoringRequestStatus::REQUESTED;
        $this->assertTrue($this->isScheduledOrPotentialSchedule());
    }
}

class TestableMentoringRequestStatus extends MentoringRequestStatus
{
    public $value;
}
