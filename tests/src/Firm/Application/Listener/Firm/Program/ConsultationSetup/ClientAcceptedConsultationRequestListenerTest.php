<?php

namespace Firm\Application\Listener\Firm\Program\ConsultationSetup;

use Firm\Application\Service\Firm\Program\ConsultationSetup\SendClientConsultationSessionMail;
use Tests\TestBase;

class ClientAcceptedConsultationRequestListenerTest extends TestBase
{
    protected $listener;
    protected $sendClientConsultationSessionMail;

    protected $event;
    protected $firmId = 'firmId', $clientId = 'clientId', $programId = 'programId', $consultationSessionId = 'consultationSessionId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->sendClientConsultationSessionMail = $this->buildMockOfClass(SendClientConsultationSessionMail::class);
        $this->listener = new ClientAcceptedConsultationRequestListener($this->sendClientConsultationSessionMail);
        
        $this->event = $this->buildMockOfInterface(ClientAcceptedConsultationRequestEventInterface::class);
        $this->event->expects($this->any())->method('getFirmId')->willReturn($this->firmId);
        $this->event->expects($this->any())->method('getClientId')->willReturn($this->clientId);
        $this->event->expects($this->any())->method('getProgramId')->willReturn($this->programId);
        $this->event->expects($this->any())->method('getConsultationSessionId')->willReturn($this->consultationSessionId);
    }
    
    public function test_handle_executeSendClientConsultationSessionMail()
    {
        $this->sendClientConsultationSessionMail->expects($this->once())
                ->method('execute')
                ->with($this->firmId, $this->clientId, $this->programId, $this->consultationSessionId);
        
        $this->listener->handle($this->event);
    }
}
