<?php

namespace Client\Application\Listener;

use Tests\TestBase;

class ClientRegisteredListenerTest extends TestBase
{
    protected $listener;
    protected $event, $clientRegistrantId = 'clientRegistrantId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->listener = new ClientRegisteredListener();
        
        $this->event = $this->buildMockOfInterface(ClientRegisteredEventInterface::class);
        $this->event->expects($this->any())->method('getClientRegistrantId')->willReturn($this->clientRegistrantId);
    }
    public function test_handle_storeClientRegistrantIdInListener()
    {
        $this->listener->handle($this->event);
        $this->assertEquals($this->clientRegistrantId, $this->listener->getClientRegistrantId());
    }
}
