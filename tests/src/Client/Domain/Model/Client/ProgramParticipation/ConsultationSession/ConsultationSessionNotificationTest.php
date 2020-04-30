<?php

namespace Client\Domain\Model\Client\ProgramParticipation\ConsultationSession;

use Client\Domain\Model\Client\{
    ClientNotification,
    ProgramParticipation\ConsultationSession
};
use Tests\TestBase;

class ConsultationSessionNotificationTest extends TestBase
{

    protected $consultationSession;
    protected $clientNotification;
    protected $id = 'newId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->clientNotification = $this->buildMockOfClass(ClientNotification::class);
    }

    public function test_construct_setProperties()
    {
        $consultationSessionNotification = new TestableConsultationSessionNotification(
                $this->consultationSession, $this->id, $this->clientNotification);
        $this->assertEquals($this->consultationSession, $consultationSessionNotification->consultationSession);
        $this->assertEquals($this->id, $consultationSessionNotification->id);
        $this->assertEquals($this->clientNotification, $consultationSessionNotification->clientNotification);
    }

}

class TestableConsultationSessionNotification extends ConsultationSessionNotification
{

    public $consultationSession;
    public $id;
    public $clientNotification;

}
