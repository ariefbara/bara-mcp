<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession;

use Personnel\Domain\Model\Firm\ {
    Personnel\PersonnelNotification,
    Program\Participant\ConsultationSession
};
use Tests\TestBase;

class PersonnelNotificationOnConsultationSessionTest extends TestBase
{
    protected $consultationSession;
    protected $personnelNotification;
    protected $id = 'newId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->personnelNotification = $this->buildMockOfClass(PersonnelNotification::class);
    }
    
    public function test_construct_setProperties()
    {
        $notification = new TestablePersonnelNotificationOnConsultationSession($this->consultationSession, $this->id, $this->personnelNotification);
        $this->assertEquals($this->consultationSession, $notification->consultationSession);
        $this->assertEquals($this->id, $notification->id);
        $this->assertEquals($this->personnelNotification, $notification->personnelNotification);
    }
}

class TestablePersonnelNotificationOnConsultationSession extends PersonnelNotificationOnConsultationSession
{
    public $consultationSession;
    public $id;
    public $personnelNotification;
}
