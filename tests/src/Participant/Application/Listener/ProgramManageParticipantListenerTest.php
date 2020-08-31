<?php

namespace Client\Application\Listener;

use Client\Application\Service\Client\ProgramParticipation\ParticipantNotificationAdd;
use Tests\TestBase;

class ProgramManageParticipantListenerTest extends TestBase
{
    protected $clientNotificationRepository;
    protected $programParticipationRepository;
    protected $listener;
    protected $event;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientNotificationRepository = $this->buildMockOfInterface(ClientNotificationRepository::class);
        $this->programParticipationRepository = $this->buildMockOfInterface(ProgramParticipationRepository::class);
        $this->listener = new ProgramManageParticipantListener(
                $this->clientNotificationRepository, $this->programParticipationRepository);
        
        $this->event = $this->buildMockOfInterface(ProgramManageParticipantEventInterface::class);
    }
    
    public function test_handle_executeParticipantNotificationAdd()
    {
        $this->clientNotificationRepository->expects($this->once())
                ->method('add');
        $this->listener->handle($this->event);
    }
}
