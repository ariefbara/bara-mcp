<?php
namespace Resources\Domain\Model;

use Tests\TestBase;

class ContainEventTest extends TestBase
{
    protected $entity;
    protected $event;
    protected function setUp(): void {
        parent::setUp();
        $this->entity = new TestableEntityContainEvent();
        $this->event = $this->getMockBuilder("Resources\Application\Event\Event")->getMock();
    }
    
    function test_recordEvent_addEventToRecordedEvents() {
        $this->entity->executeRecordEvent($this->event);
        $this->assertEquals($this->event, $this->entity->getRecordedEvents()[0]);
    }
    function test_clearRecordedEvents_clearRecordedEvents() {
        $this->entity->executeRecordEvent($this->event);
        $this->entity->clearRecordedEvents();
        $this->assertEmpty($this->entity->getRecordedEvents());
    }
}

class TestableEntityContainEvent{
    use ContainEvents;
    
    public function executeRecordEvent($event) {
        $this->recordEvent($event);
    }
}