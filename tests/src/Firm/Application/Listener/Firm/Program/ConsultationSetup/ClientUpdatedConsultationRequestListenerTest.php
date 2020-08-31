<?php

namespace Firm\Application\Listener\Firm\Program\ConsultationSetup;

use Firm\Application\Service\Firm\Program\ConsultationSetup\SendClientConsultationRequestMail;
use Tests\TestBase;

class ClientUpdatedConsultationRequestListenerTest extends TestBase
{
    protected $listener;
    protected $sendClientConsultationRequestMail;
    protected $event;
    
    protected $firmId = 'firmId', $clientId = 'clientId', $programId = 'programId', $consultationRequestId = 'consultationRequestId';


    protected function setUp(): void
    {
        parent::setUp();
        $this->sendClientConsultationRequestMail = $this->buildMockOfClass(SendClientConsultationRequestMail::class);
        $this->listener = new ClientUpdatedConsultationRequestListener($this->sendClientConsultationRequestMail);
        
        $this->event = $this->buildMockOfInterface(ClientUpdatedConsultationRequestEventInterface::class);
        $this->event->expects($this->any())->method('getFirmId')->willReturn($this->firmId);
        $this->event->expects($this->any())->method('getClientId')->willReturn($this->clientId);
        $this->event->expects($this->any())->method('getProgramId')->willReturn($this->programId);
        $this->event->expects($this->any())->method('getConsultationRequestId')->willReturn($this->consultationRequestId);
    }
    
    public function test_handle_executeSendClientConsultationRequestMail()
    {
        $this->sendClientConsultationRequestMail->expects($this->once())
                ->method('execute')
                ->with($this->firmId, $this->clientId, $this->programId, $this->consultationRequestId);
        $this->listener->handle($this->event);
    }
}
