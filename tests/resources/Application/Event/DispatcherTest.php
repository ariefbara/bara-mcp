<?php
namespace Resources\Application\Event;

use Tests\TestBase;

class DispatcherTest extends TestBase
{
    protected $dispatcher;
    protected $listener;
    protected $event, $eventName = 'eventName', $containEvents;
    
    protected function setUp(): void {
        parent::setUp();
        $this->listener = $this->getMockBuilder('\Resources\Application\Event\Listener')->getMock();
        $this->dispatcher = new TestableDispatcher();
        $this->dispatcher->listeners[$this->eventName][] = $this->listener;
        
        $this->event = $this->getMockBuilder('\Resources\Application\Event\Event')->getMock();
        $this->event->expects($this->any())->method('getName')->willReturn($this->eventName);
        $this->containEvents = $this->getMockBuilder('\Resources\Application\Event\ContainEvents')->getMock();
    }
    
    function test_addListener_addListenerToList() {
        $listener = $this->getMockBuilder('\Resources\Application\Event\Listener')->getMock();
        $this->dispatcher->addListener($eventName = 'otherEventName', $listener);
        $this->assertEquals($listener, $this->dispatcher->listeners[$eventName]['0']);
    }
    function test_addListener_sameListenerOnSimmilarEventNameAlreadyInList_ignoreAddition() {
        $this->dispatcher->addListener($this->eventName, $this->listener);
        $this->assertEquals(1, count($this->dispatcher->listeners[$this->eventName]));
    }
    
    function test_dispatch_executeListenerHandleMethod() {
        $this->containEvents->expects($this->once())->method('getRecordedEvents')->willReturn([$this->event]);
        $this->listener->expects($this->once())->method("handle")->with($this->event);
        
        $this->dispatcher->dispatch($this->containEvents);
    }
    function test_dispatch_clearEntityRecordedEvent() {
        $this->containEvents->expects($this->once())->method('getRecordedEvents')->willReturn([$this->event]);
        $this->containEvents->expects($this->once())->method('clearRecordedEvents');
        $this->dispatcher->dispatch($this->containEvents);
    }
}

class TestableDispatcher extends Dispatcher{
    public $listeners;
}

