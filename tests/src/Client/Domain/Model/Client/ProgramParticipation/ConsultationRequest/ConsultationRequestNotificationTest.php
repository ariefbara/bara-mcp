<?php

namespace Client\Domain\Model\Client\ProgramParticipation\ConsultationRequest;

use Client\Domain\Model\Client\{
    ClientNotification,
    ProgramParticipation\ConsultationRequest
};
use Tests\TestBase;

class ConsultationRequestNotificationTest extends TestBase
{

    protected $consultationRequest;
    protected $clientNotification;
    protected $id = 'newId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->clientNotification = $this->buildMockOfClass(ClientNotification::class);
    }

    public function test_construct_setProperties()
    {
        $consutationRequestNotification = new TestableConsultationRequestNotification(
                $this->consultationRequest, $this->id, $this->clientNotification);
        $this->assertEquals($this->consultationRequest, $consutationRequestNotification->consultationRequest);
        $this->assertEquals($this->id, $consutationRequestNotification->id);
        $this->assertEquals($this->clientNotification, $consutationRequestNotification->clientNotification);
    }

}

class TestableConsultationRequestNotification extends ConsultationRequestNotification
{

    public $consultationRequest;
    public $id;
    public $clientNotification;

}
