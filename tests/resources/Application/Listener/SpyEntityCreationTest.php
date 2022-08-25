<?php

namespace Resources\Application\Listener;

use Resources\Domain\Event\CommonEvent;
use Tests\TestBase;

class SpyEntityCreationTest extends TestBase
{
    protected $listener;
    protected $event, $entityId = 'entity-id';

    protected function setUp(): void
    {
        parent::setUp();
        $this->listener = new TestableSpyEntityCreation();
        $this->event = new CommonEvent('event-name', $this->entityId);
    }
    
    public function test_handle_setId()
    {
        $this->listener->handle($this->event);
        $this->assertEquals($this->entityId, $this->listener->entityId);
    }
}

class TestableSpyEntityCreation extends SpyEntityCreation
{

    public $entityId;

}
