<?php

namespace Shared\Domain\Model;

class ConsultationRequestStatusVOTest extends \Tests\TestBase
{
    protected $value = 'proposed';
    
    protected function setUp(): void
    {
        parent::setUp();
    }
    
    protected function executeConstruct()
    {
        return new TestableConsultationRequestStatusVO($this->value);
    }
    public function test_execute_setProperties()
    {
        $vo = $this->executeConstruct();
        $this->assertEquals($this->value, $vo->value);
    }
    public function test_execute_invalidStatus_throwEx()
    {
        $this->value = 'invalid status';
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "bad request: invalid consultation request status";
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }
    public function test_execute_otherValidStatus_void()
    {
        $this->value = 'rejected';
        $this->executeConstruct();
        $this->value = 'cancelled';
        $this->executeConstruct();
        $this->value = 'offered';
        $this->executeConstruct();
        $this->value = 'scheduled';
        $this->executeConstruct();
        $this->markAsSuccess();
    }
}

class TestableConsultationRequestStatusVO extends ConsultationRequestStatusVO
{
    public $value;
}
