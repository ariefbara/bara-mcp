<?php

namespace Client\Application\Listener;

use Tests\TestBase;

class ConsultantMutateConsultationSessionListenerTest extends TestBase
{

    protected $clientNotificationRepository;
    protected $consultationSessionRepository;
    protected $listener;
    protected $event;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientNotificationRepository = $this->buildMockOfInterface(ClientNotificationRepository::class);
        $this->consultationSessionRepository = $this->buildMockOfInterface(ConsultationSessionRepository::class);
        $this->listener = new ConsultantMutateConsultationSessionListener(
                $this->clientNotificationRepository, $this->consultationSessionRepository);

        $this->event = $this->buildMockOfInterface(ConsultantMutateConsultationSessionEventInterface::class);
    }

    public function test_handle_executeConsultationSessionNotificationAdd()
    {
        $this->clientNotificationRepository->expects($this->once())
                ->method('add');
        $this->listener->handle($this->event);
    }

}
