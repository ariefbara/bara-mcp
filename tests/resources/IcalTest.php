<?php

namespace Resources;

use DateTimeImmutable;
use Eluceo\iCal\Component\Calendar;
use Eluceo\iCal\Component\Event;
use Tests\TestBase;

class IcalTest extends TestBase
{
    protected $ical;
    protected $event;
    protected $calendar;

    protected $id = "event-id", $useTimezone = false;
    protected $summary = 'event summary';
    protected $startTime;
    protected $endTime;
    protected $sequence = 1;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ical = new TestableIcal('id');
        $this->event = $this->buildMockOfClass(Event::class);
        $this->ical->event = $this->event;
        $this->calendar = $this->buildMockOfClass(Calendar::class);
        $this->ical->calendar = $this->calendar;
        
        $this->startTime = new DateTimeImmutable("+1 hours");
        $this->endTime = new DateTimeImmutable("+2 hours");
    }

    public function test_construct_setProperties()
    {
        $ical = new TestableIcal($this->id, $this->useTimezone);
        $event = new Event($this->id);
        $event->setUseTimezone($this->useTimezone);
        $this->assertEquals($event, $ical->event);
        
        $calendar = new Calendar("innov.id");
        $calendar->addEvent($event);
        $this->assertEquals($calendar, $ical->calendar);
    }
    
    public function test_render_returnCalendarRenderResult()
    {
        $this->calendar->expects($this->once())
                ->method('render')
                ->willReturn($content = 'ical content');
        $this->assertEquals($content, $this->ical->render());
    }
    
    public function test_setSummary_setEventSummary()
    {
        $this->event->expects($this->once())
                ->method('setSummary')
                ->with($this->summary);
        $this->ical->setSummary($this->summary);
    }
    public function test_setSummary_returnSelf()
    {
        $this->assertEquals($this->ical, $this->ical->setSummary($this->summary));
    }
    
    protected function setDtStart()
    {
        return $this->ical->setDtStart($this->startTime);
    }
    public function test_setDtStart_setEventDtStart()
    {
        $this->event->expects($this->once())
                ->method('setDtStart')
                ->with($this->startTime);
        $this->setDtStart();
    }
    public function test_setDtStart_returnSelf()
    {
        $this->assertEquals($this->ical, $this->setDtStart());
    }
    
    protected function setDtEnd()
    {
        return $this->ical->setDtEnd($this->endTime);
    }
    public function test_setDtEnd_setEventDtEnd()
    {
        $this->event->expects($this->once())
                ->method('setDtEnd')
                ->with($this->endTime);
        $this->setDtEnd();
    }
    public function test_setDtEnd_returnSelf()
    {
        $this->assertEquals($this->ical, $this->setDtEnd());
    }
    
    protected function setSequence()
    {
        return $this->ical->setSequence($this->sequence);
    }
    public function test_setSequence_setEventSequence()
    {
        $this->event->expects($this->once())
                ->method('setSequence')
                ->with($this->sequence);
        $this->setSequence();
    }
    public function test_setSequence_returnSelf()
    {
        $this->assertEquals($this->ical, $this->setSequence());
    }
    
    protected function setCancelled()
    {
        return $this->ical->setCancelled();
    }
    public function test_setCancelled_setEventCancelled()
    {
        $this->event->expects($this->once())
                ->method('setCancelled')
                ->with(true);
        $this->setCancelled();
    }
    public function test_setCancelled_returnSelf()
    {
        $this->assertEquals($this->ical, $this->setCancelled());
    }
}

class TestableIcal extends Ical
{
    public $event;
    public $calendar;
}
