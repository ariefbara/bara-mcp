<?php

namespace Client\Domain\Model\Client;

use Client\Domain\Model\{
    Client,
    Client\ProgramParticipation\ConsultationRequest,
    Client\ProgramParticipation\ConsultationSession,
    Client\ProgramParticipation\Worksheet\Comment
};
use Tests\TestBase;

class ClientNotificationTest extends TestBase
{

    protected $clientNotification;
    protected $client;
    protected $id = 'newId', $message = 'new message';
    protected $consultationRequest;
    protected $consultationSession;
    protected $comment;
    protected $programParticipation;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->buildMockOfClass(Client::class);
        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->comment = $this->buildMockOfClass(Comment::class);
        $this->programParticipation = $this->buildMockOfClass(ProgramParticipation::class);

        $this->clientNotification = TestableClientNotification::notificationForComment(
                        $this->client, 'id', 'message', $this->comment);
    }

    public function test_notificationForConsultationRequest_setProperties()
    {
        $notification = TestableClientNotification::notificationForConsultationRequest(
                        $this->client, $this->id, $this->message, $this->consultationRequest);
        $this->assertEquals($this->client, $notification->client);
        $this->assertEquals($this->id, $notification->id);
        $this->assertEquals($this->message, $notification->message);
        $this->assertEquals(\Resources\DateTimeImmutableBuilder::buildYmdHisAccuracy(), $notification->notifiedTime);
        $this->assertEquals($this->consultationRequest, $notification->consultationRequest);
        $this->assertFalse($notification->read);
        $this->assertNull($notification->consultationSession);
        $this->assertNull($notification->comment);
    }

    public function test_notificationForConsultationSession_setProperties()
    {
        $notification = TestableClientNotification::notificationForConsultationSession(
                        $this->client, $this->id, $this->message, $this->consultationSession);
        $this->assertEquals($this->consultationSession, $notification->consultationSession);
    }

    public function test_notificationForComment_setProperties()
    {
        $notification = TestableClientNotification::notificationForComment(
                        $this->client, $this->id, $this->message, $this->comment);
        $this->assertEquals($this->comment, $notification->comment);
    }
    public function test_notificationForProgramParticipation_setProperties()
    {
        $notification = TestableClientNotification::notificationForProgramParticipation(
                        $this->client, $this->id, $this->message, $this->programParticipation);
        $this->assertEquals($this->programParticipation, $notification->programParticipation);
    }

    public function test_readSetReadStatusTrue()
    {
        $this->clientNotification->read();
        $this->assertTrue($this->clientNotification->read);
    }

}

class TestableClientNotification extends ClientNotification
{

    public $client;
    public $id;
    public $message;
    public $read = false;
    public $notifiedTime;
    public $consultationRequest = null;
    public $consultationSession = null;
    public $comment = null;
    public $programParticipation = null;

}
