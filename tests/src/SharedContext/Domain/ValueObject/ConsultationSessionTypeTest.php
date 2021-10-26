<?php

namespace SharedContext\Domain\ValueObject;

use Tests\TestBase;

class ConsultationSessionTypeTest extends TestBase
{
    protected $consultationSessionType;
    protected $sessionType;
    protected $approvedByMentor = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSessionType = new TestableConsultationSessionType(ConsultationSessionType::DECLARED_TYPE, null);
        $this->sessionType = ConsultationSessionType::HANDSHAKING_TYPE;
    }
    
    protected function construct()
    {
        return new TestableConsultationSessionType($this->sessionType, $this->approvedByMentor);
    }
    public function test_construct_setProperties()
    {
        $type = $this->construct();
        $this->assertSame(ConsultationSessionType::HANDSHAKING_TYPE, $type->sessionType);
        $this->assertNull($type->approvedByMentor);
    }
    public function test_construct_invalidType_badRequest()
    {
        $this->sessionType = 33;
        $this->assertRegularExceptionThrowed(function() {
            $this->construct();
        }, 'Bad Request', 'bad request: invalid consultation session type argument');
    }
    
    protected function canBeCancelled()
    {
        return $this->consultationSessionType->canBeCancelled();
    }
    public function test_canBeCancelled_returnTrue()
    {
        $this->assertTrue($this->canBeCancelled());
    }
    public function test_canBeCancelled_handshakingType_returnFalse()
    {
        $this->consultationSessionType->sessionType = ConsultationSessionType::HANDSHAKING_TYPE;
        $this->assertFalse($this->canBeCancelled());
    }
    
    protected function deny()
    {
        return $this->consultationSessionType->deny();
    }
    public function test_deny_returnDeniedConsultationSessionType()
    {
        $result = $this->deny();
        $this->assertSame($this->consultationSessionType->sessionType, $result->sessionType);
        $this->assertFalse($result->approvedByMentor);
    }
    public function test_deny_nonDeclaredType_forbidden()
    {
        $this->consultationSessionType->sessionType = ConsultationSessionType::HANDSHAKING_TYPE;
        $this->assertRegularExceptionThrowed(function() {
            $this->deny();
        }, 'Forbidden', 'forbidden: unable to deny session (either session is non declare type or already approved/denied)');
    }
    public function test_deny_approvalAlreadySubmitted_forbidden()
    {
        $this->consultationSessionType->approvedByMentor = false;
        $this->assertRegularExceptionThrowed(function() {
            $this->deny();
        }, 'Forbidden', 'forbidden: unable to deny session (either session is non declare type or already approved/denied)');
    }
    
    protected function approve()
    {
        return $this->consultationSessionType->approve();
    }
    public function test_approve_returnApprovedSessionType()
    {
        $result = $this->approve();
        $this->assertSame($this->consultationSessionType->sessionType, $result->sessionType);
        $this->assertTrue($result->approvedByMentor);
    }
    public function test_approve_nonDeclaredType_forbidden()
    {
        $this->consultationSessionType->sessionType = ConsultationSessionType::HANDSHAKING_TYPE;
        $this->assertRegularExceptionThrowed(function() {
            $this->approve();
        }, 'Forbidden', 'forbidden: unable to deny session (either session is non declare type or already approved/denied)');
    }
    public function test_approve_alreadyApproved_forbidden()
    {
        $this->consultationSessionType->approvedByMentor = true;
        $this->assertRegularExceptionThrowed(function() {
            $this->approve();
        }, 'Forbidden', 'forbidden: unable to deny session (either session is non declare type or already approved/denied)');
    }
    
    protected function getSessionTypeDisplayValue()
    {
        return $this->consultationSessionType->getSessionTypeDisplayValue();
    }
    public function test_getSessionTypeDisplayValue_declaredType_returnCorrespondString()
    {
        $this->assertEquals('DECLARED', $this->getSessionTypeDisplayValue());
    }
    public function test_getSessionTypeDisplayValue_handsakingType_returnCorrespondString()
    {
        $this->consultationSessionType->sessionType = ConsultationSessionType::HANDSHAKING_TYPE;
        $this->assertEquals('HANDSHAKING', $this->getSessionTypeDisplayValue());
    }
}

class TestableConsultationSessionType extends ConsultationSessionType
{
    public $sessionType;
    public $approvedByMentor;
}
