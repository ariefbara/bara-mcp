<?php
namespace Resources\Application\Event;

use Tests\TestBase;

class DispatcherTest extends TestBase
{
    protected $dispatcher;
    protected $strongConsistencyMode = true;
    protected $listener;
    protected $event, $eventName = 'eventName', $containEvents;
    
    protected function setUp(): void {
        parent::setUp();
        $this->dispatcher = new TestableDispatcher();
        
        $this->listener = $this->getMockBuilder('\Resources\Application\Event\Listener')->getMock();
        $this->dispatcher->listeners[$this->eventName][] = $this->listener;
        
        $this->event = $this->getMockBuilder('\Resources\Application\Event\Event')->getMock();
        $this->event->expects($this->any())->method('getName')->willReturn($this->eventName);
        $this->containEvents = $this->getMockBuilder('\Resources\Application\Event\ContainEvents')->getMock();
    }
    
    public function test_construct_setProperties()
    {
        $dispatcher = new TestableDispatcher($this->strongConsistencyMode);
        $this->assertEquals($this->strongConsistencyMode, $dispatcher->strongConsistencyMode);
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
    
    protected function executeDispatch()
    {
        $this->dispatcher->dispatch($this->containEvents);
    }
    
    function test_dispatch_executeListenerHandleMethod() {
        $this->containEvents->expects($this->once())->method('pullRecordedEvents')->willReturn([$this->event]);
        $this->listener->expects($this->once())->method("handle")->with($this->event);
        $this->executeDispatch();
    }
    public function test_dispatch_eventualConsistencyMode_addToDispatchedList()
    {
        $this->containEvents->expects($this->once())->method('pullRecordedEvents')->willReturn([$this->event]);
        $this->dispatcher->strongConsistencyMode = false;
        $this->executeDispatch();
        $this->assertEquals([$this->event], $this->dispatcher->dispatchedEvents);
    }
    
    public function test_execute_askAllListenerToHadleDispatchedEvents()
    {
        $this->dispatcher->dispatchedEvents = [$this->event];
        $this->listener->expects($this->once())->method("handle")->with($this->event);
        $this->dispatcher->execute();
    }
}

class TestableDispatcher extends Dispatcher{
    public $strongConsistencyMode;
    public $listeners;
    public $dispatchedEvents;
}

