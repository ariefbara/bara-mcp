<?php

namespace Personnel\Domain\Model\Firm\Personnel;

use Personnel\Domain\Model\Firm\{
    Personnel,
    Personnel\ProgramConsultant\ConsultationRequest,
    Personnel\ProgramConsultant\ConsultationSession
};
use Resources\DateTimeImmutableBuilder;
use Tests\TestBase;

class PersonnelNotificationTest extends TestBase
{

    protected $personnel;
    protected $consultationRequest;
    protected $consultationSession;
    protected $personnelNotification;
    protected $id = 'personnelNotificationId', $message = 'message';

    protected function setUp(): void
    {
        parent::setUp();
        $this->personnel = $this->buildMockOfClass(Personnel::class);
        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->personnelNotification = TestablePersonnelNotification::notificationForConsultationRequest(
                        $this->personnel, 'id', 'message', $this->consultationRequest);
    }

    public function test_notificationforConsultationRequest_setProperties()
    {
        $notification = TestablePersonnelNotification::notificationForConsultationRequest(
                        $this->personnel, $this->id, $this->message, $this->consultationRequest);
        $this->assertEquals($this->personnel, $notification->personnel);
        $this->assertEquals($this->id, $notification->id);
        $this->assertEquals($this->message, $notification->message);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $notification->notifiedTime);
        $this->assertfalse($notification->read);

        $this->assertEquals($this->consultationRequest, $notification->consultationRequest);
    }

    public function test_notificationforConsultationSession_setConsultationSession()
    {
        $notification = TestablePersonnelNotification::notificationForConsultationSession(
                        $this->personnel, $this->id, $this->message, $this->consultationSession);

        $this->assertEquals($this->consultationSession, $notification->consultationSession);
    }

    public function test_read_readNotification()
    {

        $this->personnelNotification->read();
        $this->assertTrue($this->personnelNotification->read);
    }

}

class TestablePersonnelNotification extends PersonnelNotification
{

    public $personnel;
    public $id;
    public $message;
    public $read = false;
    public $notifiedTime;
    public $consultationRequest = null;
    public $consultationSession = null;

}
