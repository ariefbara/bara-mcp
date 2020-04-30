<?php

namespace Client\Domain\Model\Client\ProgramParticipation;

use Client\Domain\Model\Client\ {
    ClientNotification,
    ProgramParticipation
};
use Tests\TestBase;

class ParticipantNotificationTest extends TestBase
{

    protected $programParticipation;
    protected $clientNotification;
    protected $participantNotification;
    protected $id = 'nextParticipantNotificationId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->programParticipation = $this->buildMockOfClass(ProgramParticipation::class);
        $this->clientNotification = $this->buildMockOfClass(ClientNotification::class);

        $this->participantNotification = new TestableParticipantNotification(
                $this->programParticipation, 'id',
                $this->clientNotification);
    }

    public function test_construct_setProperties()
    {
        $participantNotification = new TestableParticipantNotification($this->programParticipation, $this->id,
                $this->clientNotification);
        $this->assertEquals($this->programParticipation, $participantNotification->programParticipation);
        $this->assertEquals($this->id, $participantNotification->id);
        $this->assertEquals($this->clientNotification, $participantNotification->clientNotification);
    }

}

class TestableParticipantNotification extends ParticipantNotification
{

    public $programParticipation, $id, $clientNotification;

}
