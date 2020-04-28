<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequest;

use Personnel\Domain\Model\Firm\Personnel\ {
    PersonnelNotification,
    ProgramConsultant\ConsultationRequest
};
use Tests\TestBase;

class PersonnelNotificationOnConsultationRequestTest extends TestBase
{
    protected $consultationRequest;
    protected $personnelNotification;
    protected $id = 'newId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->personnelNotification = $this->buildMockOfClass(PersonnelNotification::class);
    }
    
    public function test_construct_setProperties()
    {
        $notification = new TestablePersonnelNotificationOnConsultationRequest($this->consultationRequest, $this->id, $this->personnelNotification);
        $this->assertEquals($this->consultationRequest, $notification->consultationRequest);
        $this->assertEquals($this->id, $notification->id);
        $this->assertEquals($this->personnelNotification, $notification->personnelNotification);
    }
}

class TestablePersonnelNotificationOnConsultationRequest extends PersonnelNotificationOnConsultationRequest
{
    public $consultationRequest;
    public $id;
    public $personnelNotification;
}
