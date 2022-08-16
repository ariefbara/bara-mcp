<?php

namespace SharedContext\Domain\ValueObject;

use Tests\TestBase;

class ParticipantStatusTest extends TestBase
{

    protected $participantStatus;
    protected $autoAccept = true, $programPrice = 4000;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participantStatus = new TestableParticipantStatus(false, null);
    }
    
    protected function construct()
    {
        return new TestableParticipantStatus($this->autoAccept, $this->programPrice);
    }
    public function test_construct_setProperties()
    {
        $status = $this->construct();
        $this->assertSame(ParticipantStatus::SETTLEMENT_REQUIRED, $status->status);
    }
    public function test_construct_manualAccept_setStatusRegistered()
    {
        $this->autoAccept = false;
        $status = $this->construct();
        $this->assertSame(ParticipantStatus::REGISTERED, $status->status);
    }
    public function test_construct_freeAutoAcceptProgram_setActive()
    {
        $this->programPrice = null;
        $status = $this->construct();
        $this->assertSame(ParticipantStatus::ACTIVE, $status->status);
    }
    public function test_construct_freeManualAcceptProgram_setRegistered()
    {
        $this->programPrice = null;
        $this->autoAccept = false;
        $status = $this->construct();
        $this->assertSame(ParticipantStatus::REGISTERED, $status->status);
    }
    
    protected function acceptRegistrant()
    {
        return $this->participantStatus->acceptRegistrant($this->programPrice);
    }
    public function test_acceptRegistrant_registeredParticipantOfPaidProgram_returnSettlementRequiredParticipant()
    {
        $status = $this->acceptRegistrant();
        $this->assertEquals(ParticipantStatus::SETTLEMENT_REQUIRED, $status->status);
    }
    public function test_acceptRegistrant_registeredParticipantOfFreeProgram_returnActiveParticipant()
    {
        $this->programPrice = null;
        $status = $this->acceptRegistrant();
        $this->assertEquals(ParticipantStatus::ACTIVE, $status->status);
    }
    public function test_acceptRegistrant_nonRegisteredParticipant_forbidden()
    {
        $this->participantStatus->status = ParticipantStatus::SETTLEMENT_REQUIRED;
        $this->assertRegularExceptionThrowed(function () {
            $this->acceptRegistrant();
        }, 'Forbidden', 'can only process registered applicant');
    }
    
    protected function rejectRegistrant()
    {
        return $this->participantStatus->rejectRegistrant();
    }
    public function test_rejectRegistrant_returnRejectedParticipant()
    {
        $status = $this->rejectRegistrant();
        $this->assertEquals(ParticipantStatus::REJECTED, $status->status);
    }
    public function test_rejectRegistrant_nonRegisteredParticipant_forbidden()
    {
        $this->participantStatus->status = ParticipantStatus::SETTLEMENT_REQUIRED;
        $this->assertRegularExceptionThrowed(function () {
            $this->rejectRegistrant();
        }, 'Forbidden', 'can only process registered applicant');
    }
    
    protected function cancelApplication()
    {
        return $this->participantStatus->cancelApplication();
    }
    public function test_cancelApplication_returnCancelledParticipant()
    {
        $status = $this->cancelApplication();
        $this->assertEquals(ParticipantStatus::CANCELLED, $status->status);
    }
    public function test_cancelApplication_notInRegistrationState_forbidden()
    {
        $this->participantStatus->status = ParticipantStatus::ACTIVE;
        $this->assertRegularExceptionThrowed(function () {
            $this->cancelApplication();
        }, 'Forbidden', 'can only process active applicant');
    }
    public function test_cancelApplication_inApplicantState_settlementRequired()
    {
        $this->participantStatus->status = ParticipantStatus::SETTLEMENT_REQUIRED;
        $this->cancelApplication();
        $this->markAsSuccess();
    }
    
    protected function qualify()
    {
        return $this->participantStatus->qualify();
    }
    public function test_qualify_returnCompletedParticipant()
    {
        $this->participantStatus->status = ParticipantStatus::ACTIVE;
        $status = $this->qualify();
        $this->assertEquals(ParticipantStatus::COMPLETED, $status->status);
    }
    public function test_qualify_nonActiveParticipant_forbidden()
    {
        $this->assertRegularExceptionThrowed(function () {
            $this->qualify();
        }, 'Forbidden', 'can only qualify active participant');
    }
    
    protected function executeFail()
    {
        return $this->participantStatus->fail();
    }
    public function test_fail_returnFailedParticipant()
    {
        $this->participantStatus->status = ParticipantStatus::ACTIVE;
        $status = $this->executeFail();
        $this->assertEquals(ParticipantStatus::FAILED, $status->status);
    }
    public function test_fail_nonActiveParticipant_forbidden()
    {
        $this->assertRegularExceptionThrowed(function () {
            $this->executeFail();
        }, 'Forbidden', 'can only fail active participant');
    }
    
    protected function settlePayment()
    {
        return $this->participantStatus->settlePayment();
    }
    public function test_settlePayment_returnActiveParticipant()
    {
        $this->participantStatus->status = ParticipantStatus::SETTLEMENT_REQUIRED;
        $status = $this->settlePayment();
        $this->assertEquals(ParticipantStatus::ACTIVE, $status->status);
    }
    public function test_settlePayment_notInSettlementState_forbidden()
    {
/*
        $this->assertRegularExceptionThrowed(function () {
            $this->settleApplication();
        }, 'Forbidden', 'unable to process, no settlement required');
 * 
 */
        $this->markTestSkipped('in conflict with external business process');
    }
    
    protected function statusEquals()
    {
        return $this->participantStatus->statusEquals(ParticipantStatus::REGISTERED);
    }
    public function test_statusEquals_sameStatus_returnTrue()
    {
        $this->assertTrue($this->statusEquals());
    }
    public function test_statusEquals_differentStatus_returnFalse()
    {
        $this->participantStatus->status = ParticipantStatus::SETTLEMENT_REQUIRED;
        $this->assertFalse($this->statusEquals());
    }
    
    protected function isActiveRegistrantOrParticipant()
    {
        return $this->participantStatus->isActiveRegistrantOrParticipant();
    }
    public function test_isActiveRegistrantOrParticipant_activeRegistrant_returnTrue()
    {
        $this->assertTrue($this->isActiveRegistrantOrParticipant());
    }
    public function test_isActiveRegistrantOrParticipant_inactiveRegistrantOrParticipant_returnFalse()
    {
        $this->participantStatus->status = ParticipantStatus::CANCELLED;
        $this->assertFalse($this->isActiveRegistrantOrParticipant());
    }
    public function test_isActiveRegistrantOrParticipant_activeRegistrant_settlementRequired_returnTrue()
    {
        $this->participantStatus->status = ParticipantStatus::SETTLEMENT_REQUIRED;
        $this->assertTrue($this->isActiveRegistrantOrParticipant());
    }
    public function test_isActiveRegistrantOrParticipant_activeParticipant_returnTrue()
    {
        $this->participantStatus->status = ParticipantStatus::ACTIVE;
        $this->assertTrue($this->isActiveRegistrantOrParticipant());
    }

}

class TestableParticipantStatus extends ParticipantStatus
{
    public $status;
}
