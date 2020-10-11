<?php

namespace Resources\Domain\Model;

use Resources\Domain\Event\CommonEvent;
use Tests\TestBase;

class EntityContainCommonEventsTest extends TestBase
{
    protected $entity;
    protected $event;

    protected function setUp(): void
    {
        parent::setUp();
        $this->entity = new TestableEntityContainCommonEvents();
        $this->event = $this->buildMockOfClass(CommonEvent::class);
        $this->entity->recordedEvents[] = $this->event;
    }
    
    public function test_recordEvent_addEventToRecordedEventsList()
    {
        $this->entity->recordEvent($this->event);
        $this->assertEquals($this->event, $this->entity->recordedEvents[1]);
    }
    
    protected function executePullRecordedEvent()
    {
        return $this->entity->pullRecordedEvents();
    }
    
    public function test_pullRecordedEvents_returnRecordedEventsList()
    {
        $this->assertEquals($this->entity->recordedEvents, $this->executePullRecordedEvent());
    }
    public function test_pullRecordedEvent_emptyRecordedEventList()
    {
        $this->executePullRecordedEvent();
        $this->assertEquals([], $this->entity->recordedEvents);
    }
}

class TestableEntityContainCommonEvents extends EntityContainCommonEvents
{
    public $recordedEvents;
    
    function recordEvent(CommonEvent $commonEvent): void
    {
        parent::recordEvent($commonEvent);
    }
}
