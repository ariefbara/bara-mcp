<?php

namespace Resources\Domain\Model;

use Resources\Application\Event\ContainEvents;
use Resources\Application\Event\Event;
use Tests\TestBase;

class ContainAggregatedEntitiesHavingEventsTest extends TestBase
{
    protected $aggregate;
    protected $entityOne;
    protected $entityTwo;
    protected $event;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->aggregate = new TestableContainAggregatedEntitiesHavingEvents();
        
        $this->event = $this->buildMockOfInterface(Event::class);
        $this->aggregate->recordedEvents[] = $this->event;
        
        $this->entityOne = $this->buildMockOfInterface(ContainEvents::class);
        $this->entityTwo = $this->buildMockOfInterface(ContainEvents::class);
        $this->aggregate->aggregatedEntitiesHavingEvents[] = $this->entityOne;
        $this->aggregate->aggregatedEntitiesHavingEvents[] = $this->entityTwo;
        
    }
    
    protected function executePullRecordedEvents()
    {
        return $this->aggregate->pullRecordedEvents();
    }
    public function test_pullRecordedEvents_returnAllEventsAndPulledEventsOccuredInAggregatedEntities()
    {
        $this->entityOne->expects($this->once())
                ->method('pullRecordedEvents')
                ->willReturn([$this->event]);
        $this->entityTwo->expects($this->once())
                ->method('pullRecordedEvents')
                ->willReturn([$this->event]);
        $this->assertEquals([$this->event, $this->event, $this->event], $this->executePullRecordedEvents());
    }
    public function test_pullRecordedEvents_clearAggregatedEntitiesList()
    {
        $this->executePullRecordedEvents();
        $this->assertEmpty($this->aggregate->aggregatedEntitiesHavingEvents);
    }
    
    public function test_recordEvent_addEventToRecordedEventsList()
    {
        $this->aggregate->recordEvent($this->event);
        $this->assertEquals($this->event, $this->aggregate->recordedEvents[1]);
    }
    
    public function test_recordEntityHavingEvents_addEntityToCollection()
    {
        $entity = $this->buildMockOfInterface(ContainEvents::class);
        $this->aggregate->recordEntityHavingEvents($entity);
        $this->assertEquals([$this->entityOne, $this->entityTwo, $entity], $this->aggregate->aggregatedEntitiesHavingEvents);
    }
}

class TestableContainAggregatedEntitiesHavingEvents extends ContainAggregatedEntitiesHavingEvents
{
    public $aggregatedEntitiesHavingEvents = [];
    public $recordedEvents = [];
    
    function recordEvent(Event $event): void
    {
        parent::recordEvent($event);
    }
    
    function recordEntityHavingEvents(ContainEvents $entity): void
    {
        parent::recordEntityHavingEvents($entity);
    }
}
