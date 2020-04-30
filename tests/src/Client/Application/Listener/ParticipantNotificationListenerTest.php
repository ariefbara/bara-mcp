<?php

namespace Client\Application\Listener;

use Client\Application\Service\Client\ProgramParticipation\ParticipantNotificationAdd;
use Tests\TestBase;

class ParticipantNotificationListenerTest extends TestBase
{
    protected $listener;
    protected $participantNotificationAdd;
    protected $event;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participantNotificationAdd = $this->buildMockOfClass(ParticipantNotificationAdd::class);
        $this->listener = new ParticipantNotificationListener($this->participantNotificationAdd);
        
        $this->event = $this->buildMockOfInterface(ParticipantNotificationEventInterface::class);
    }
    
    public function test_handle_executeParticipantNotificationAdd()
    {
        $this->participantNotificationAdd->expects($this->once())
                ->method('execute');
        $this->listener->handle($this->event);
    }
}
