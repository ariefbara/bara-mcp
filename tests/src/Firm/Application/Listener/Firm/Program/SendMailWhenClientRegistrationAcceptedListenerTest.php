<?php

namespace Firm\Application\Listener\Firm\Program;

use Firm\Application\Service\Firm\Program\SendClientRegistrationAcceptedMail;
use Tests\TestBase;

class SendMailWhenClientRegistrationAcceptedListenerTest extends TestBase
{
    protected $listener;
    protected $sendClientRegistrationAcceptedMail;
    
    protected $event;
    protected $firmId = 'firmId', $programId = 'programId', $clientId = 'clientId';


    protected function setUp(): void
    {
        parent::setUp();
        $this->sendClientRegistrationAcceptedMail = $this->buildMockOfClass(SendClientRegistrationAcceptedMail::class);
        $this->listener = new SendMailWhenClientRegistrationAcceptedListener($this->sendClientRegistrationAcceptedMail);
        
        $this->event = $this->buildMockOfInterface(ClientRegistrationAcceptedEventInterface::class);
        $this->event->expects($this->any())->method('getFirmId')->willReturn($this->firmId);
        $this->event->expects($this->any())->method('getProgramId')->willReturn($this->programId);
        $this->event->expects($this->any())->method('getClientId')->willReturn($this->clientId);
    }
    
    public function test_handle_executeSndClientRegistrationAcceptedMail()
    {
        $this->sendClientRegistrationAcceptedMail->expects($this->once())
                ->method('execute')
                ->with($this->firmId, $this->programId, $this->clientId);
        $this->listener->handle($this->event);
    }
}
